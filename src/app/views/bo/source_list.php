<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des sources - Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="has-nav">
    <?= renderBackofficeNav('sources') ?>

    <main class="main-content">
        <h1>Liste des sources</h1>

        <?php if (isset($_GET['created'])): ?>
            <div class="alert alert-success">Source ajoutee avec succes.</div>
        <?php endif; ?>

        <p class="mt-2">
            <a class="btn" href="/backoffice/?action=source_add">Ajouter une source</a>
        </p>

        <?php if (empty($sources)): ?>
            <p>Aucune source trouvee.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sources as $source): ?>
                        <tr>
                            <td><?= (int)$source['id_source'] ?></td>
                            <td><?= e((string)$source['valeur']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
