<?php
$searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$selectedTheme = isset($_GET['theme']) ? trim((string) $_GET['theme']) : '';
$selectedPeriod = isset($_GET['periode']) ? trim((string) $_GET['periode']) : '';

$imageByCategory = [
    'diplomatie' => 'https://images.unsplash.com/photo-1560439514-4e9645039924?auto=format&fit=crop&w=900&q=80',
    'humanitaire' => 'https://images.unsplash.com/photo-1516574187841-cb9cc2ca948b?auto=format&fit=crop&w=900&q=80',
    'energie' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=900&q=80',
    'defense' => 'https://images.unsplash.com/photo-1543007630-9710e4a00a20?auto=format&fit=crop&w=1200&q=80',
    'international' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=900&q=80',
    'default' => 'https://images.unsplash.com/photo-1543007630-9710e4a00a20?auto=format&fit=crop&w=1200&q=80'
];

if (!function_exists('truncate_text')) {
    function truncate_text(string $text, int $limit): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($text, 'UTF-8') > $limit
                ? mb_substr($text, 0, $limit - 3, 'UTF-8') . '...'
                : $text;
        }

        return strlen($text) > $limit
            ? substr($text, 0, $limit - 3) . '...'
            : $text;
    }
}

if (!function_exists('relative_time')) {
    function relative_time(string $datetime): string
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
            return 'Il y a ' . (int) floor($delta / 60) . ' min';
        }
        if ($delta < 86400) {
            return 'Il y a ' . (int) floor($delta / 3600) . ' h';
        }

        return 'Il y a ' . (int) floor($delta / 86400) . ' j';
    }
}

if (!function_exists('build_card')) {
    function build_card(array $row, array $imageByCategory): array
    {
        $category = trim((string) ($row['categorie'] ?? 'International'));
        $content = trim((string) ($row['valeur'] ?? ''));
        $source = trim((string) ($row['source'] ?? 'Redaction'));
        $categoryKey = strtolower($category);
        $image = $imageByCategory[$categoryKey] ?? $imageByCategory['default'];

        return [
            'rubrique' => $category !== '' ? $category : 'International',
            'titre' => truncate_text($content, 110),
            'resume' => truncate_text($content, 190),
            'auteur' => 'Par ' . ($source !== '' ? $source : 'Redaction'),
            'horaire' => relative_time((string) ($row['date_'] ?? '')),
            'image' => $image
        ];
    }
}

$availableThemes = [];
$rows = [];

try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        getenv('DB_HOST') ?: 'db',
        getenv('DB_PORT') ?: '5432',
        getenv('DB_NAME') ?: 'news_db'
    );

    $pdo = new PDO(
        $dsn,
        getenv('DB_USER') ?: 'news_user',
        getenv('DB_PASSWORD') ?: 'password',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $where = ['a.statut = TRUE'];
    $params = [];

    if ($searchQuery !== '') {
        $where[] = 'a.valeur ILIKE :q';
        $params[':q'] = '%' . $searchQuery . '%';
    }

    if ($selectedTheme !== '') {
        $where[] = 'c.valeur ILIKE :theme';
        $params[':theme'] = $selectedTheme;
    }

    if ($selectedPeriod === 'today') {
        $where[] = 'a.date_ >= NOW() - INTERVAL \'' . '1 day' . '\'';
    } elseif ($selectedPeriod === 'week') {
        $where[] = 'a.date_ >= NOW() - INTERVAL \'' . '7 days' . '\'';
    } elseif ($selectedPeriod === 'month') {
        $where[] = 'a.date_ >= NOW() - INTERVAL \'' . '30 days' . '\'';
    }

    $sql = 'SELECT a.id, a.valeur, a.date_, COALESCE(c.valeur, \'' . 'International' . '\') AS categorie, '
        . 'COALESCE(s.valeur, \'' . 'Redaction' . '\') AS source '
        . 'FROM article a '
        . 'LEFT JOIN categorie_information c ON c.id_categorie = a.id_categorie '
        . 'LEFT JOIN source s ON s.id_source = a.id_source '
        . 'WHERE ' . implode(' AND ', $where) . ' '
        . 'ORDER BY a.date_ DESC '
        . 'LIMIT 20';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    $themesStatement = $pdo->query('SELECT DISTINCT valeur FROM categorie_information WHERE TRIM(valeur) <> \'' . '\' ORDER BY valeur ASC');
    $themeRows = $themesStatement->fetchAll(PDO::FETCH_COLUMN);
    foreach ($themeRows as $themeLabel) {
        $label = trim((string) $themeLabel);
        if ($label !== '') {
            $availableThemes[$label] = $label;
        }
    }
} catch (Throwable $e) {
    $rows = [];
}

if (!empty($rows)) {
    $une = build_card($rows[0], $imageByCategory);

    $flashs = [];
    foreach (array_slice($rows, 1, 3) as $row) {
        $flashs[] = [
            'titre' => truncate_text((string) ($row['valeur'] ?? ''), 90),
            'horaire' => relative_time((string) ($row['date_'] ?? ''))
        ];
    }

    $articles = [];
    foreach (array_slice($rows, 4, 6) as $row) {
        $articles[] = build_card($row, $imageByCategory);
    }
} else {
    $une = [
        'rubrique' => 'Dossier special Iran',
        'titre' => 'Aucune donnee en base pour le moment',
        'resume' => 'Ajoute des articles dans PostgreSQL pour alimenter automatiquement la une, les flashs et les cartes.',
        'auteur' => 'Par Redaction',
        'horaire' => 'Mise a jour en attente',
        'image' => $imageByCategory['default']
    ];

    $flashs = [
        ['titre' => 'Base de donnees vide: aucun flash a afficher', 'horaire' => 'En attente'],
        ['titre' => 'Insere des lignes dans la table article', 'horaire' => 'En attente'],
        ['titre' => 'Les filtres fonctionneront des que les donnees seront disponibles', 'horaire' => 'En attente']
    ];

    $articles = [
        [
            'rubrique' => 'Information',
            'titre' => 'Charge les articles depuis PostgreSQL',
            'resume' => 'Cette interface est deja connectee a Docker/PostgreSQL et attend simplement des donnees a afficher.',
            'image' => $imageByCategory['default']
        ]
    ];
}

$searchTitle = $searchQuery !== ''
    ? 'Resultats sur la guerre en Iran: ' . $searchQuery
    : 'Guerre en Iran: suivi en direct';

require_once __DIR__ . '/../../inc/header.php';
?>

<h1 class="search-page-title"><?php echo htmlspecialchars($searchTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

<section class="hero">
    <article class="card hero-main">
        <img class="hero-media" src="<?php echo htmlspecialchars($une['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image de la une">
        <span class="tag"><?php echo htmlspecialchars($une['rubrique'], ENT_QUOTES, 'UTF-8'); ?></span>
        <h1><?php echo htmlspecialchars($une['titre'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?php echo htmlspecialchars($une['resume'], ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="meta"><?php echo htmlspecialchars($une['auteur'], ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($une['horaire'], ENT_QUOTES, 'UTF-8'); ?></div>
    </article>

    <aside class="card side-list">
        <h2 class="section-title">Flash info</h2>
        <?php foreach ($flashs as $flash): ?>
            <article class="side-item">
                <h3><?php echo htmlspecialchars($flash['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="meta"><?php echo htmlspecialchars($flash['horaire'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
        <?php endforeach; ?>
    </aside>
</section>

<section>
    <h2 class="section-title">Dernieres evolutions du conflit</h2>
    <div class="grid">
        <?php foreach ($articles as $article): ?>
            <article class="card article">
                <img class="article-media" src="<?php echo htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image article <?php echo htmlspecialchars($article['rubrique'], ENT_QUOTES, 'UTF-8'); ?>">
                <span class="tag"><?php echo htmlspecialchars($article['rubrique'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h3><?php echo htmlspecialchars($article['titre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($article['resume'], ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="#">Lire l article</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="card newsletter">
    <h2>Newsletter quotidienne</h2>
    <p>Recevez chaque matin un resume des informations importantes.</p>
    <form action="#" method="post">
        <input type="email" name="email" placeholder="Votre email" required>
        <button type="submit">Je m inscris</button>
    </form>
</section>

<?php require_once __DIR__ . '/../../inc/footer.php';
