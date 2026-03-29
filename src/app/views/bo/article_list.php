<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des articles — Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
    <style>
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .article-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            transition: border-color var(--transition);
        }

        .article-card:hover {
            border-color: var(--border-dark);
        }

        .article-card__meta {
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .article-card__id {
            font-family: var(--font-mono);
            font-size: .75rem;
            color: var(--text-muted);
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: .1rem .45rem;
        }

        .article-card__badge {
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--text-muted);
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: .1rem .5rem;
        }

        .article-card__date {
            margin-left: auto;
            font-size: .8rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .article-card__content {
            font-size: .95rem;
            line-height: 1.6;
            color: var(--text);
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .article-card__source {
            font-size: .82rem;
            color: var(--text-muted);
            font-style: italic;
            border-top: 1px solid var(--border);
            padding-top: .6rem;
            margin-top: auto;
        }
    </style>
</head>
<body class="has-nav">
    <?= renderBackofficeNav('articles') ?>

    <main class="main-content">
        <h1>Liste des articles</h1>

        <?php if (empty($articles)): ?>
            <p>Aucun article trouvé.</p>
        <?php else: ?>
            <div class="articles-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="article-card">
                        <div class="article-card__meta">
                            <span class="article-card__id">#<?= (int)$article['id'] ?></span>
                            <?php if (!empty($article['categorie'])): ?>
                                <span class="article-card__badge"><?= e((string)$article['categorie']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($article['date_'])): ?>
                                <span class="article-card__date"><?= e((string)$article['date_']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="article-card__content">
                            <?= (string)($article['valeur'] ?? '') ?>
                        </div>

                        <?php if (!empty($article['source'])): ?>
                            <div class="article-card__source">
                                Source : <?= e((string)$article['source']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>