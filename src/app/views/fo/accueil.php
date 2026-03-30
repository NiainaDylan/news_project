<?php
$une = [
    'categorie' => 'Dossier special Iran',
    'titre' => 'Guerre en Iran: la pression internationale s intensifie apres une nouvelle nuit de frappes',
    'resume' => 'Les discussions diplomatiques se poursuivent tandis que plusieurs capitales appellent a une desescalade immediate du conflit.',
    'auteur' => 'Par Sarah Benali',
    'horaire' => 'Mise a jour il y a 35 min',
    'image' => 'https://images.unsplash.com/photo-1543007630-9710e4a00a20?auto=format&fit=crop&w=1200&q=80'
];

$flashs = [
    ['titre' => 'ONU: reunion d urgence du Conseil de securite convoquee', 'horaire' => 'Il y a 12 min'],
    ['titre' => 'Teheran: communications perturbees dans plusieurs quartiers', 'horaire' => 'Il y a 26 min'],
    ['titre' => 'Petrole: le Brent depasse un nouveau seuil hebdomadaire', 'horaire' => 'Il y a 41 min']
];

$articles = [
    [
        'rubrique' => 'Diplomatie',
        'titre' => 'Mediations regionales: trois pays proposent une feuille de route',
        'resume' => 'Des negociateurs travaillent sur un mecanisme de cessez-le-feu progressif et de supervision internationale.',
        'image' => 'https://images.unsplash.com/photo-1560439514-4e9645039924?auto=format&fit=crop&w=900&q=80'
    ],
    [
        'rubrique' => 'Humanitaire',
        'titre' => 'Aide d urgence: des corridors humanitaires en discussion',
        'resume' => 'Les ONG demandent un acces securise pour acheminer medicaments, eau et equipements de premiere necessite.',
        'image' => 'https://images.unsplash.com/photo-1516574187841-cb9cc2ca948b?auto=format&fit=crop&w=900&q=80'
    ],
    [
        'rubrique' => 'Energie',
        'titre' => 'Gaz et petrole: les marches europeens sous tension',
        'resume' => 'Les analystes anticipent une forte volatilite des prix tant que la situation securitaire restera instable.',
        'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=900&q=80'
    ]
];

$searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$searchTitle = $searchQuery !== '' ? 'Resultats sur la guerre en Iran: ' . $searchQuery : 'Guerre en Iran: suivi en direct';

require_once __DIR__ . '/../../inc/header.php';
?>

<h1 class="search-page-title"><?php echo htmlspecialchars($searchTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

<section class="hero">
    <article class="card hero-main">
        <img class="hero-media" src="<?php echo htmlspecialchars($une['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image de la une">
        <span class="tag"><?php echo htmlspecialchars($une['categorie'], ENT_QUOTES, 'UTF-8'); ?></span>
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
