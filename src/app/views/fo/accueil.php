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
        $title = foExtractTitle($contentHtml);
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

        return [
            'id' => (int)($row['id'] ?? 0),
            'slug' => foSlugify($title),
            'url' => '/pages/' . rawurlencode(foSlugify($title)) . '-' . (int)($row['id'] ?? 0) . '.html',
            'rubrique' => trim((string)($row['categorie'] ?? 'Sans categorie')),
            'titre' => $title,
            'resume' => foTruncate($plain, 200),
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
        $where[] = 'a.valeur ILIKE :q';
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

    if ($selectedPeriod === 'today') {
        $where[] = "a.date_ >= NOW() - INTERVAL '1 day'";
    } elseif ($selectedPeriod === 'week') {
        $where[] = "a.date_ >= NOW() - INTERVAL '7 days'";
    } elseif ($selectedPeriod === 'month') {
        $where[] = "a.date_ >= NOW() - INTERVAL '30 days'";
    } else {
        $where[] = "a.date_ >= date_trunc('month', NOW()) AND a.date_ < (date_trunc('month', NOW()) + INTERVAL '1 month')";
    }

    if (empty($where)) {
        $where[] = '1=1';
    }

    $sql = "SELECT a.id,
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
    static fn(array $card): array => ['titre' => $card['titre'], 'horaire' => $card['horaire']],
    array_slice($cards, 1, 3)
);
$articles = array_slice($cards, 4, 9);

$searchTitle = $searchQuery !== ''
    ? 'Resultats de recherche: ' . $searchQuery
    : 'Actualites en direct';

if ($selectedArticleId > 0 && $une !== null) {
    $expectedSlug = (string)($une['slug'] ?? 'article');
    if ($requestedSlug === '' || $requestedSlug !== $expectedSlug) {
        header('Location: /pages/' . rawurlencode($expectedSlug) . '-' . (int)$une['id'] . '.html', true, 301);
        exit;
    }

    $searchTitle = 'Lecture: ' . (string)$une['titre'];
}



$lastModifiedTs = !empty($rows) && !empty($rows[0]['date_']) ? (strtotime((string)$rows[0]['date_']) ?: time()) : time();
header('Cache-Control: public, max-age=60, stale-while-revalidate=120');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModifiedTs) . ' GMT');
$ifModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime((string)$_SERVER['HTTP_IF_MODIFIED_SINCE']) : false;
if ($ifModifiedSince !== false && $ifModifiedSince >= $lastModifiedTs) {
    http_response_code(304);
    exit;
}

require_once __DIR__ . '/../../inc/header.php';
?>

<h1 class="search-page-title"><?php echo htmlspecialchars($searchTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if ($une !== null): ?>
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
                        <h3><?php echo htmlspecialchars($flash['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="meta"><?php echo htmlspecialchars($flash['horaire'], ENT_QUOTES, 'UTF-8'); ?></p>
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
    <h2 class="section-title">Derniers articles</h2>
    <div class="grid">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <article class="card article">
                    <img class="article-media" src="<?php echo htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($article['image_alt'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (!empty($article['is_image_expired'])): ?>
                        <span class="image-expired-badge">Image expiree (date_cache depassee)</span>
                    <?php endif; ?>
                    <span class="tag"><?php echo htmlspecialchars($article['rubrique'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <h3><?php echo htmlspecialchars($article['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($article['resume'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="/pages/<?php echo rawurlencode((string)$article['slug']); ?>-<?php echo (int)$article['id']; ?>.html">Lire l article</a>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <article class="card article">
                <h3>Aucun article correspondant aux filtres</h3>
                <p>Essaie de modifier la recherche, la categorie, la source, le statut ou la periode.</p>
            </article>
        <?php endif; ?>
    </div>
</section>



<?php require_once __DIR__ . '/../../inc/footer.php';
