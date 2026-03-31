<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter une categorie - Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="has-nav">
    <?= renderBackofficeNav('categories') ?>

    <main class="main-content">
        <h1>Ajouter une categorie</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/backoffice/?action=categorie_add_save">
            <div>
                <label for="valeur">Nom de la categorie</label>
                <input type="text" id="valeur" name="valeur" maxlength="50" required>
            </div>

            <div>
                <button type="submit">Enregistrer</button>
            </div>
        </form>
    </main>
</body>
</html>
