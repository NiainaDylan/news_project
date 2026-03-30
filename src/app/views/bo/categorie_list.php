<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des categories - Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="has-nav">
    <?= renderBackofficeNav('categories') ?>

    <main class="main-content">
        <h1>Liste des categories</h1>

        <?php if (isset($_GET['created'])): ?>
            <div class="alert alert-success">Categorie ajoutee avec succes.</div>
        <?php endif; ?>

        <p class="mt-2">
            <a class="btn" href="/backoffice/?action=categorie_add">Ajouter une categorie</a>
        </p>

        <?php if (empty($categories)): ?>
            <p>Aucune categorie trouvee.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $categorie): ?>
                        <tr>
                            <td><?= (int)$categorie['id_categorie'] ?></td>
                            <td><?= e((string)$categorie['valeur']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
