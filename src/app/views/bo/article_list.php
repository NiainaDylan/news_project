<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des articles — Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
    <style>
        
    </style>
</head>
<body class="has-nav">
    <?= renderBackofficeNav('articles') ?>

    <main class="main-content">
        <h1>Liste des articles</h1>

        <form id="article-filters" class="filters-panel" autocomplete="off">
            <div class="filters-panel__row">
                <div class="filters-panel__group filters-panel__group--full">
                    <span class="filters-panel__label">Catégories</span>
                    <div class="category-choices">
                        <label class="category-choices__item">
                            <input type="radio" name="id_categorie" value="" checked>
                            <span>Toutes</span>
                        </label>
                        <?php foreach ($categories as $category): ?>
                            <label class="category-choices__item">
                                <input type="radio" name="id_categorie" value="<?= (int)$category['id_categorie'] ?>">
                                <span><?= e((string)$category['valeur']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filters-panel__group">
                    <label class="filters-panel__label" for="filter-source">Source</label>
                    <select id="filter-source" name="id_source">
                        <option value="">Toutes</option>
                        <?php foreach ($sources as $source): ?>
                            <option value="<?= (int)$source['id_source'] ?>"><?= e((string)$source['valeur']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filters-panel__group">
                    <label class="filters-panel__label" for="filter-period">Période</label>
                    <select id="filter-period" name="periode">
                        <option value="mois_courant_auto" selected>Mois actuel</option>
                    </select>
                </div>

                <div class="filters-panel__group filters-panel__group--full">
                    <span class="filters-panel__label">Statut</span>
                    <div class="status-choices">
                        <label class="status-choices__item">
                            <input type="radio" name="statut" value="" checked>
                            <span>Tous</span>
                        </label>
                        <label class="status-choices__item">
                            <input type="radio" name="statut" value="1">
                            <span>Actif</span>
                        </label>
                        <label class="status-choices__item">
                            <input type="radio" name="statut" value="0">
                            <span>Inactif</span>
                        </label>
                    </div>
                </div>

                <div class="filters-panel__group">
                    <label class="filters-panel__label" for="filter-limit">N insertions</label>
                    <input type="number" id="filter-limit" name="limit_insertion" value="10" min="1" max="200" required>
                </div>

                <div class="filters-panel__actions">
                    <button type="submit">Filtrer</button>
                    <button type="button" class="secondary" id="filters-reset">Réinitialiser</button>
                </div>
            </div>
        </form>

        <p id="filters-feedback" class="filters-feedback"></p>

        <div id="articles-container">
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
        </div>
    </main>

    <script src="/backoffice/assets/js/filtre.js"></script>
</body>
</html>