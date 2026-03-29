<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des articles — Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="has-nav">
    <?= renderBackofficeNav('articles') ?>

    <main class="main-content">
        <h1>Liste des articles</h1>

        <?php if (empty($articles)): ?>
            <p>Aucun article trouvé.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Source</th>
                        <th>Catégorie</th>
                        <th>Date</th>
                        <th>Contenu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?= (int)$article['id'] ?></td>
                            <td><?= e((string)($article['source'] ?? '')) ?></td>
                            <td><?= e((string)($article['categorie'] ?? '')) ?></td>
                            <td><?= e((string)($article['date_'] ?? '')) ?></td>
                            <td><?= (string)($article['valeur'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>