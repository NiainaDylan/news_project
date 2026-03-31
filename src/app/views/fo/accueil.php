<?php
declare(strict_types=1);

require_once __DIR__ . '/../../inc/db.php';
require_once __DIR__ . '/../../inc/functions.php';

$searchQuery = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$selectedCategoryId = isset($_GET['id_categorie']) ? (int)$_GET['id_categorie'] : 0;
$selectedSourceId = isset($_GET['id_source']) ? (int)$_GET['id_source'] : 0;
$selectedPeriod = isset($_GET['periode']) ? trim((string)$_GET['periode']) : 'mois_courant_auto';
$selectedArticleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$requestedSlug = isset($_GET['slug']) ? trim((string)$_GET['slug']) : '';

$fallbackImage = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="900" height="500"><rect width="100%25" height="100%25" fill="%23e9edf2"/><text x="50%25" y="50%25" text-anchor="middle" fill="%23525a68" font-size="24" font-family="Arial">ActuFlash</text></svg>';

if (!function_exists('foTruncate')) {
    function foTruncate(string $text, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        if ($text === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($text, 'UTF-8') > $limit
                ? mb_substr($text, 0, $limit - 3, 'UTF-8') . '...'
                : $text;
        }

        return strlen($text) > $limit ? substr($text, 0, $limit - 3) . '...' : $text;
    }
}

if (!function_exists('foRelativeTime')) {
    function foRelativeTime(string $datetime): string
    {
        $timestamp = strtotime($datetime);
        if ($timestamp === false) {
            return 'Mise a jour recente';
        }

        $delta = time() - $timestamp;
        if ($delta < 60) {
            return 'A l instant';
        }
        if ($delta < 3600) {
            return 'Il y a ' . (int)floor($delta / 60) . ' min';
        }
        if ($delta < 86400) {
            return 'Il y a ' . (int)floor($delta / 3600) . ' h';
        }

        return 'Il y a ' . (int)floor($delta / 86400) . ' j';
    }
}

if (!function_exists('foTinyText')) {
    function foTinyText(string $html): string
    {
        $withoutTags = strip_tags(html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        return trim(preg_replace('/\s+/u', ' ', $withoutTags) ?? $withoutTags);
    }
}

if (!function_exists('foExtractTitle')) {
    function foExtractTitle(string $html): string
    {
        if (preg_match('/<h[1-3][^>]*>(.*?)<\/h[1-3]>/is', $html, $matches) === 1) {
            $candidate = foTinyText((string)$matches[1]);
            if ($candidate !== '') {
                return foTruncate($candidate, 120);
            }
        }

        $text = foTinyText($html);
        if ($text === '') {
            return 'Article sans titre';
        }

        $sentence = preg_split('/(?<=[\.!?])\s+/u', $text, 2)[0] ?? $text;
        return foTruncate($sentence, 120);
    }
}

if (!function_exists('foBuildSummary')) {
    function foBuildSummary(string $plainText, string $title, int $limit = 200): string
    {
        $plainText = trim($plainText);
        $title = trim($title);
        if ($plainText === '') {
            return '';
        }

        $summary = $plainText;
        if ($title !== '') {
            if (function_exists('mb_stripos') && function_exists('mb_strlen') && function_exists('mb_substr')) {
                if (mb_stripos($plainText, $title, 0, 'UTF-8') === 0) {
                    $summary = trim((string)mb_substr($plainText, mb_strlen($title, 'UTF-8'), null, 'UTF-8'));
                }
            } elseif (stripos($plainText, $title) === 0) {
                $summary = trim(substr($plainText, strlen($title)));
            }

            $summary = preg_replace('/^[\s\.,;:!\?\-\|]+/u', '', $summary) ?? $summary;
        }

        if ($summary === '') {
            return 'Cliquez pour lire l article complet.';
        }

        return foTruncate($summary, $limit);
    }
}

if (!function_exists('foSlugify')) {
    function foSlugify(string $value): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'article';
    }
}

if (!function_exists('foSanitizeArticleHtml')) {
    function foSanitizeArticleHtml(string $html): string
    {
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? $html;
        $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html) ?? $html;
        $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/\s(href|src)\s*=\s*("|\')\s*javascript:[^"\']*("|\')/i', '', $html) ?? $html;

        return strip_tags($html, '<p><br><strong><em><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><a><img><figure><figcaption>');
    }
}

if (!function_exists('foStripFirstImageTag')) {
    function foStripFirstImageTag(string $html): string
    {
        return preg_replace('/<img\b[^>]*>/i', '', $html, 1) ?? $html;
    }
}

if (!function_exists('foComparableText')) {
    function foComparableText(string $value): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;
        return trim($value);
    }
}

if (!function_exists('foStripLeadingDuplicateTitleBlock')) {
    function foStripLeadingDuplicateTitleBlock(string $html, string $title): string
    {
        $titleNorm = foComparableText($title);
        if ($titleNorm === '') {
            $html = preg_replace('/<h1(\b[^>]*)>/i', '<h2$1>', $html) ?? $html;
            $html = preg_replace('/<\/h1>/i', '</h2>', $html) ?? $html;
            return $html;
        }

        $workingHtml = ltrim($html);
        for ($i = 0; $i < 4; $i++) {
            if (preg_match('/^\s*<(h[1-6]|p|div)[^>]*>(.*?)<\/\1>/is', $workingHtml, $matches) !== 1) {
                break;
            }

            $firstBlockText = foTinyText((string)($matches[2] ?? ''));
            $firstNorm = foComparableText($firstBlockText);
            if ($firstNorm === '') {
                break;
            }

            $isDuplicate = $firstNorm === $titleNorm
                || str_starts_with($firstNorm, $titleNorm . ' ')
                || str_starts_with($titleNorm, $firstNorm . ' ');

            if (!$isDuplicate) {
                break;
            }

            $workingHtml = ltrim((string)substr($workingHtml, strlen((string)$matches[0])));
        }

        $workingHtml = preg_replace('/<h1(\b[^>]*)>/i', '<h2$1>', $workingHtml) ?? $workingHtml;
        $workingHtml = preg_replace('/<\/h1>/i', '</h2>', $workingHtml) ?? $workingHtml;

        return $workingHtml;
    }
}

if (!function_exists('foExtractImageFromHtml')) {
    function foExtractImageFromHtml(string $html): string
    {
        $decodedHtml = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $decodedHtml, $matches) === 1) {
            return trim((string)$matches[1]);
        }

        if (preg_match('#((?:https?://|/|\.\./|\./)?[^\s"\'>]+\.(?:png|jpe?g|gif|webp|svg))(\?[^\s"\'>]*)?#i', $decodedHtml, $matches) === 1) {
            return trim((string)$matches[0]);
        }

        return '';
    }
}

if (!function_exists('foExtractLocalCacheFromHtml')) {
    function foExtractLocalCacheFromHtml(string $html): string
    {
        $decodedHtml = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (preg_match('/<img[^>]+data-local-cache=["\']([^"\']+)["\']/i', $decodedHtml, $matches) === 1) {
            return trim((string)$matches[1]);
        }

        return '';
    }
}

if (!function_exists('foExtractImageAltFromHtml')) {
    function foExtractImageAltFromHtml(string $html): string
    {
        if (preg_match('/<img[^>]+alt=["\']([^"\']*)["\']/i', $html, $matches) === 1) {
            return trim((string)$matches[1]);
        }

        return '';
    }
}

if (!function_exists('foNormalizeImageSrc')) {
    function foNormalizeImageSrc(string $src, string $fallbackImage): string
    {
        $src = html_entity_decode(trim($src), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($src === '') {
            return $fallbackImage;
        }

        if (preg_match('#^https?://#i', $src) === 1 || str_starts_with($src, 'data:image/')) {
            return $src;
        }

        if (str_starts_with($src, './')) {
            $src = substr($src, 1);
        }

        if (str_starts_with($src, '../')) {
            $src = '/' . ltrim(preg_replace('#^(\.\./)+#', '', $src) ?? $src, '/');
        }

        if (str_starts_with($src, '/var/www/html/')) {
            return '/' . ltrim(substr($src, strlen('/var/www/html/')), '/');
        }

        if (preg_match('#^[A-Za-z]:\\\\#', $src) === 1) {
            $src = str_replace('\\\\', '/', $src);
            $pos = stripos($src, '/uploads/');
            if ($pos !== false) {
                return substr($src, $pos);
            }
        }

        if (str_starts_with($src, '/')) {
            return $src;
        }

        if (str_starts_with($src, 'uploads/')) {
            return '/' . $src;
        }

        return $fallbackImage;
    }
}

if (!function_exists('foBuildCard')) {
    function foBuildCard(array $row, string $fallbackImage): array
    {
        $contentHtml = (string)($row['valeur'] ?? '');
        $plain = foTinyText($contentHtml);
        $storedTitle = trim((string)($row['title'] ?? ''));
        $title = $storedTitle !== '' ? foTruncate($storedTitle, 120) : foExtractTitle($contentHtml);
        $image = foNormalizeImageSrc(foExtractImageFromHtml($contentHtml), $fallbackImage);
        if ($image === $fallbackImage) {
            $image = foNormalizeImageSrc(foExtractLocalCacheFromHtml($contentHtml), $fallbackImage);
        }
        $dateCacheRaw = trim((string)($row['date_cache'] ?? ''));
        $cacheExpiresAt = $dateCacheRaw !== '' ? strtotime($dateCacheRaw) : false;
        $isExpired = $cacheExpiresAt !== false && $cacheExpiresAt < time();
        if ($isExpired) {
            $image = $fallbackImage;
        }
        $imageAlt = foExtractImageAltFromHtml($contentHtml);
        $detailHtml = foSanitizeArticleHtml($contentHtml);
        if (strpos($detailHtml, '<img') !== false) {
            $detailHtml = foStripFirstImageTag($detailHtml);
        }
        $detailHtml = foStripLeadingDuplicateTitleBlock($detailHtml, $title);

        return [
            'id' => (int)($row['id'] ?? 0),
            'slug' => foSlugify($title),
            'url' => '/pages/' . rawurlencode(foSlugify($title)) . '-' . (int)($row['id'] ?? 0) . '.html',
            'rubrique' => trim((string)($row['categorie'] ?? 'Sans categorie')),
            'titre' => $title,
            'resume' => foBuildSummary($plain, $title, 200),
            'contenu_html' => $detailHtml,
            'auteur' => 'Par ' . trim((string)($row['source'] ?? 'Redaction')),
            'horaire' => foRelativeTime((string)($row['date_'] ?? '')),
            'image' => $image,
            'image_alt' => $imageAlt !== '' ? $imageAlt : $title,
            'is_image_expired' => $isExpired
        ];
    }
}

$availableCategories = [];
$availableSources = [];
$rows = [];

try {
    $pdo = getPDO();

    $where = [];
    $params = [];

    if ($searchQuery !== '') {
        $where[] = '(a.valeur ILIKE :q OR a.title ILIKE :q)';
        $params[':q'] = '%' . $searchQuery . '%';
    }

    if ($selectedCategoryId > 0) {
        $where[] = 'a.id_categorie = :id_categorie';
        $params[':id_categorie'] = $selectedCategoryId;
    }

    if ($selectedSourceId > 0) {
        $where[] = 'a.id_source = :id_source';
        $params[':id_source'] = $selectedSourceId;
    }

    $where[] = 'a.statut = TRUE';

    if ($selectedArticleId > 0) {
        $where[] = 'a.id = :id';
        $params[':id'] = $selectedArticleId;
    }

    if ($selectedArticleId <= 0) {
        if ($selectedPeriod === 'today') {
            $where[] = "a.date_ >= NOW() - INTERVAL '1 day'";
        } elseif ($selectedPeriod === 'week') {
            $where[] = "a.date_ >= NOW() - INTERVAL '7 days'";
        } elseif ($selectedPeriod === 'month') {
            $where[] = "a.date_ >= NOW() - INTERVAL '30 days'";
        } else {
            $where[] = "a.date_ >= date_trunc('month', NOW()) AND a.date_ < (date_trunc('month', NOW()) + INTERVAL '1 month')";
        }
    }

    if (empty($where)) {
        $where[] = '1=1';
    }

    $sql = "SELECT a.id,
                   a.title,
                   a.valeur,
                   a.date_,
                   a.date_cache,
                   COALESCE(c.valeur, 'Sans categorie') AS categorie,
                                     COALESCE(s.valeur, 'Redaction') AS source
            FROM article a
            LEFT JOIN source s ON s.id_source = a.id_source
            LEFT JOIN categorie_information c ON c.id_categorie = a.id_categorie
            WHERE " . implode(' AND ', $where) . "
            ORDER BY a.date_ DESC, a.id DESC
            LIMIT 30";

    $statement = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        if (in_array($key, [':id_categorie', ':id_source', ':id'], true)) {
            $statement->bindValue($key, (int)$value, PDO::PARAM_INT);
            continue;
        }

        $statement->bindValue($key, (string)$value, PDO::PARAM_STR);
    }
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    $categoriesStatement = $pdo->query("SELECT id_categorie, valeur FROM categorie_information WHERE TRIM(valeur) <> '' ORDER BY valeur ASC");
    foreach ($categoriesStatement->fetchAll(PDO::FETCH_ASSOC) as $categoryRow) {
        $id = (int)($categoryRow['id_categorie'] ?? 0);
        $label = trim((string)($categoryRow['valeur'] ?? ''));
        if ($id > 0 && $label !== '') {
            $availableCategories[$id] = $label;
        }
    }

    $sourcesStatement = $pdo->query("SELECT id_source, valeur FROM source WHERE TRIM(valeur) <> '' ORDER BY valeur ASC");
    foreach ($sourcesStatement->fetchAll(PDO::FETCH_ASSOC) as $sourceRow) {
        $id = (int)($sourceRow['id_source'] ?? 0);
        $label = trim((string)($sourceRow['valeur'] ?? ''));
        if ($id > 0 && $label !== '') {
            $availableSources[$id] = $label;
        }
    }
} catch (Throwable $e) {
    $rows = [];
}

$cards = array_map(static fn(array $row): array => foBuildCard($row, $fallbackImage), $rows);
$une = $cards[0] ?? null;
$flashs = array_map(
    static fn(array $card): array => [
        'titre' => $card['titre'],
        'horaire' => $card['horaire'],
        'url' => $card['url'],
        'image' => $card['image'],
        'image_alt' => $card['image_alt']
    ],
    array_slice($cards, 1, 3)
);
$articles = array_slice($cards, 4, 9);

if ($selectedArticleId > 0 && $une !== null) {
    try {
        $relatedStmt = $pdo->prepare(
            "SELECT a.id,
                    a.title,
                    a.valeur,
                    a.date_,
                    a.date_cache,
                    COALESCE(c.valeur, 'Sans categorie') AS categorie,
                    COALESCE(s.valeur, 'Redaction') AS source
             FROM article a
             LEFT JOIN source s ON s.id_source = a.id_source
             LEFT JOIN categorie_information c ON c.id_categorie = a.id_categorie
             WHERE a.statut = TRUE
               AND a.id <> :id
               AND (c.valeur = :categorie OR :categorie = '')
             ORDER BY a.date_ DESC, a.id DESC
             LIMIT 6"
        );
        $relatedStmt->bindValue(':id', (int)$selectedArticleId, PDO::PARAM_INT);
        $relatedStmt->bindValue(':categorie', (string)($une['rubrique'] ?? ''), PDO::PARAM_STR);
        $relatedStmt->execute();
        $relatedRows = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
        $articles = array_map(static fn(array $row): array => foBuildCard($row, $fallbackImage), $relatedRows);
    } catch (Throwable $e) {
        $articles = [];
    }
}

$searchTitle = $searchQuery !== ''
    ? 'Resultats de recherche: ' . $searchQuery
    : 'Actualites en direct';

if ($selectedArticleId > 0 && $une !== null) {
    $expectedSlug = (string)($une['slug'] ?? 'article');
    if ($requestedSlug === '' || $requestedSlug !== $expectedSlug) {
        header('Location: /pages/' . rawurlencode($expectedSlug) . '-' . (int)$une['id'] . '.html', true, 301);
        exit;
    }

    $searchTitle = (string)$une['titre'];
}



$lastModifiedTs = !empty($rows) && !empty($rows[0]['date_']) ? (strtotime((string)$rows[0]['date_']) ?: time()) : time();
header('Cache-Control: public, max-age=60, stale-while-revalidate=120');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModifiedTs) . ' GMT');
$ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime((string)$_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
if ($ifModifiedSince !== false && $ifModifiedSince >= $lastModifiedTs) {
    http_response_code(304);
    exit;
}

$isDetailPage = $selectedArticleId > 0 && $une !== null;
$pageTitle = $isDetailPage
    ? ((string)$une['titre'] . ' | ActuFlash')
    : 'ActuFlash - Actualites';

$metaDescription = $isDetailPage
    ? ((string)($une['resume'] ?? 'Actualite detaillee sur ActuFlash.'))
    : ($searchQuery !== ''
        ? ('Resultats de recherche pour: ' . $searchQuery . ' sur ActuFlash.')
        : 'Suivez les actualites en direct: economie, diplomatie, energie et geopolitique sur ActuFlash.');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? trim((string)$_SERVER['HTTP_HOST']) : '';
$baseUrl = $host !== '' ? ($scheme . '://' . $host) : '';
$canonicalPath = $isDetailPage
    ? ('/pages/' . rawurlencode((string)$une['slug']) . '-' . (int)$une['id'] . '.html')
    : '/accueil';
$metaCanonical = $baseUrl !== '' ? ($baseUrl . $canonicalPath) : $canonicalPath;

$metaOgType = $isDetailPage ? 'article' : 'website';
$metaOgImageRaw = $isDetailPage ? (string)($une['image'] ?? '') : '';
if ($metaOgImageRaw !== '' && !str_starts_with($metaOgImageRaw, 'data:image/')) {
    if (preg_match('#^https?://#i', $metaOgImageRaw) === 1) {
        $metaOgImage = $metaOgImageRaw;
    } elseif (str_starts_with($metaOgImageRaw, '/')) {
        $metaOgImage = $baseUrl !== '' ? ($baseUrl . $metaOgImageRaw) : $metaOgImageRaw;
    } else {
        $metaOgImage = $baseUrl !== '' ? ($baseUrl . '/' . ltrim($metaOgImageRaw, '/')) : ('/' . ltrim($metaOgImageRaw, '/'));
    }
} else {
    $metaOgImage = '';
}

require_once __DIR__ . '/../../inc/header.php';
?>

<style>
    .article-detail {
        padding: 22px;
        border-radius: 16px;
    }

    .article-detail .tag {
        display: inline-block;
        margin-bottom: 6px;
    }

    .hero-main .hero-media,
    .article-media {
        height: auto;
        max-height: 360px;
        object-fit: contain;
        background: transparent;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .article-detail .hero-media {
        display: block;
        width: 100%;
        height: auto;
        max-height: 480px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid var(--line);
        margin: 12px 0 0;
    }

    .detail-headline {
        font-size: clamp(1.7rem, 4vw, 2.35rem);
        line-height: 1.2;
        margin: 10px 0 12px;
        letter-spacing: 0.2px;
    }

    .article-detail-content {
        margin-top: 14px;
        font-size: 1.04rem;
        line-height: 1.8;
        color: var(--ink);
    }

    .article-detail-content::after {
        content: '';
        display: block;
        clear: both;
    }

    .article-detail-content p,
    .article-detail-content ul,
    .article-detail-content ol,
    .article-detail-content blockquote,
    .article-detail-content h2,
    .article-detail-content h3 {
        margin: 0 0 14px;
    }

    .article-detail-content img {
        width: auto !important;
        max-width: 100%;
        height: auto !important;
        object-fit: contain;
        float: none !important;
        display: block;
        margin: 12px auto 16px;
        border-radius: 12px;
        border: 1px solid var(--line);
    }

    .related-title {
        margin-top: 6px;
    }

    .article-link {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .article-link h3,
    .hero-main-link h2,
    .side-item h3 a {
        color: var(--ink);
        text-decoration: none;
    }

    .article-link h3,
    .side-item h3 {
        font-weight: 700;
    }

    .article-read {
        display: inline-block;
        font-weight: 700;
        color: var(--brand-dark);
    }

    .side-item-link {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
    }

    .side-thumb {
        width: 90px;
        height: 68px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
        border: 1px solid var(--line);
    }

    .side-item-body {
        flex: 1;
        min-width: 0;
    }

    .side-item-body h3 {
        font-size: 0.9rem;
        margin: 0 0 4px;
        line-height: 1.35;
        font-weight: 700;
    }

    .side-item-body .meta {
        font-size: 0.8rem;
        margin: 0;
        color: var(--muted, #888);
    }
</style>

<h1 class="search-page-title"><?php echo htmlspecialchars($searchTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if ($selectedArticleId > 0 && $une !== null): ?>
    <section class="card article-detail">
        <span class="tag"><?php echo htmlspecialchars($une['rubrique'], ENT_QUOTES, 'UTF-8'); ?></span>
        <div class="meta"><?php echo htmlspecialchars($une['auteur'], ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($une['horaire'], ENT_QUOTES, 'UTF-8'); ?></div>
        <img class="hero-media" src="<?php echo htmlspecialchars($une['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($une['image_alt'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php if (!empty($une['is_image_expired'])): ?>
            <span class="image-expired-badge">Image expiree (date_cache depassee)</span>
        <?php endif; ?>
        <div class="article-detail-content">
            <?php echo preg_replace('/^(\\\\n\s*)+/u', '', $une['contenu_html']); ?>
        </div>
    </section>
<?php elseif ($une !== null): ?>
    <section class="hero">
        <article class="card hero-main">
            <a class="hero-main-link" href="<?php echo htmlspecialchars((string)$une['url'], ENT_QUOTES, 'UTF-8'); ?>">
                <img class="hero-media" src="<?php echo htmlspecialchars($une['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($une['image_alt'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (!empty($une['is_image_expired'])): ?>
                    <span class="image-expired-badge">Image expiree (date_cache depassee)</span>
                <?php endif; ?>
                <span class="tag"><?php echo htmlspecialchars($une['rubrique'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h2><?php echo htmlspecialchars($une['titre'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($une['resume'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="meta"><?php echo htmlspecialchars($une['auteur'], ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($une['horaire'], ENT_QUOTES, 'UTF-8'); ?></div>
            </a>
        </article>

        <aside class="card side-list">
            <h2 class="section-title">Flash info</h2>
            <?php if (!empty($flashs)): ?>
                <?php foreach ($flashs as $flash): ?>
                    <article class="side-item">
                        <a href="<?php echo htmlspecialchars((string)$flash['url'], ENT_QUOTES, 'UTF-8'); ?>" class="side-item-link">
                            <img class="side-thumb" src="<?php echo htmlspecialchars($flash['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($flash['image_alt'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="side-item-body">
                                <h3>
                                    <a href="<?php echo htmlspecialchars((string)$flash['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($flash['titre'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h3>
                                <p class="meta"><?php echo htmlspecialchars($flash['horaire'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="meta">Pas encore de flash info disponible.</p>
            <?php endif; ?>
        </aside>
    </section>
<?php else: ?>
    <section class="card">
        <h2 class="section-title">Aucun article publie</h2>
        <p class="meta">Le frontoffice est pret et attend les contenus saisis depuis le backoffice.</p>
    </section>
<?php endif; ?>

<section>
    <h2 class="section-title related-title"><?php echo $selectedArticleId > 0 ? 'Autres articles' : 'Derniers articles'; ?></h2>
    <div class="grid">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <article class="card article">
                    <a class="article-link" href="/pages/<?php echo rawurlencode((string)$article['slug']); ?>-<?php echo (int)$article['id']; ?>.html">
                        <img class="article-media" src="<?php echo htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($article['image_alt'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (!empty($article['is_image_expired'])): ?>
                            <span class="image-expired-badge">Image expiree (date_cache depassee)</span>
                        <?php endif; ?>
                        <span class="tag"><?php echo htmlspecialchars($article['rubrique'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3><?php echo htmlspecialchars($article['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($article['resume'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <span class="article-read">Lire l article</span>
                    </a>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <article class="card article">
                <h3>Aucun article correspondant aux filtres</h3>
                <p>Essaie de modifier la recherche, la categorie, la source ou la periode.</p>
            </article>
        <?php endif; ?>
    </div>
</section>



<?php require_once __DIR__ . '/../../inc/footer.php';