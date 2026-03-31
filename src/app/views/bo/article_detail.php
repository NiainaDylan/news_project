<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail article #<?= (int)$article['id'] ?> - Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="has-nav">
    <?= renderBackofficeNav('articles') ?>

    <main class="main-content">
        <?php
        $isActive = !empty($article['statut']);
        $cacheDateLabel = !empty($article['date_cache']) ? (string)$article['date_cache'] : 'Non definie';
        ?>

        <header class="detail-hero">
            <div class="detail-hero__top">
                <a class="detail-back-link" href="/backoffice/?action=article_list">&larr; Retour a la liste</a>
                <span class="article-card__id">#<?= (int)$article['id'] ?></span>
            </div>
            <h1>Detail article</h1>
            <p class="detail-hero__subtitle">Consulte les metadonnees et le contenu complet avant modification.</p>
            <div class="detail-header-actions">
                <a class="btn" href="/backoffice/?action=article_add&id=<?= (int)$article['id'] ?>">Editer</a>
                <a class="btn btn-secondary" href="/backoffice/?action=article_list">Fermer</a>
            </div>
        </header>

        <section class="detail-card">
            <div class="detail-meta-grid">
                <div class="detail-meta-item">
                    <span class="detail-label">Categorie</span>
                    <p><?= e((string)$article['categorie']) ?></p>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-label">Source</span>
                    <p><?= e((string)$article['source']) ?></p>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-label">Date publication</span>
                    <p><?= e((string)$article['date_']) ?></p>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-label">Date expiration cache</span>
                    <p><?= e($cacheDateLabel) ?></p>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-label">Statut</span>
                    <p>
                        <span class="detail-status <?= $isActive ? 'is-active' : 'is-inactive' ?>">
                            <?= $isActive ? 'Actif' : 'Inactif' ?>
                        </span>
                    </p>
                </div>
            </div>
        </section>

        <section class="detail-card">
            <h2>Contenu complet</h2>
            <div class="article-detail-content article-prose">
                <?= (string)$article['valeur'] ?>
            </div>
        </section>
    </main>
</body>
</html>
