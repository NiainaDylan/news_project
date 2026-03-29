<?php
$une = [
    'categorie' => 'A la une',
    'titre' => 'Sommet europeen: un plan energie de 10 milliards annonce',
    'resume' => 'Les chefs d etat valident un dispositif commun pour stabiliser les prix et accelerer la transition.',
    'auteur' => 'Par Lina Morel',
    'horaire' => 'Il y a 1 h',
    'image' => 'https://images.unsplash.com/photo-1593113598332-cd59a93b613c?auto=format&fit=crop&w=1200&q=80'
];

$flashs = [
    ['titre' => 'Metro: trafic retabli sur la ligne 8', 'horaire' => 'Il y a 20 min'],
    ['titre' => 'Ligue 1: victoire surprise de Toulouse', 'horaire' => 'Il y a 35 min'],
    ['titre' => 'IA generative: nouveau cadre europeen en discussion', 'horaire' => 'Il y a 50 min']
];

$articles = [
    [
        'rubrique' => 'Politique',
        'titre' => 'Reforme territoriale: les maires demandent plus de moyens',
        'resume' => 'Les elus locaux alertent sur la pression budgetaire et souhaitent un calendrier plus progressif.',
        'image' => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?auto=format&fit=crop&w=900&q=80'
    ],
    [
        'rubrique' => 'Economie',
        'titre' => 'PME: les commandes repartent dans l industrie',
        'resume' => 'Le dernier barometre trimestriel affiche une hausse des carnets de commande de 6%. ',
        'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=900&q=80'
    ],
    [
        'rubrique' => 'Tech',
        'titre' => 'Cybersecurite: une campagne nationale de prevention lancee',
        'resume' => 'Le gouvernement publie des recommandations simples pour limiter les fraudes numeriques.',
        'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=900&q=80'
    ]
];

$searchQuery = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$searchTitle = $searchQuery !== '' ? 'Resultats pour: ' . $searchQuery : 'Recherche d actualites';

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
    <h2 class="section-title">Dernieres actualites</h2>
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
