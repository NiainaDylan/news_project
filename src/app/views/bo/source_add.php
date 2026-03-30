<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter une source - Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="has-nav">
    <?= renderBackofficeNav('sources') ?>

    <main class="main-content">
        <h1>Ajouter une source</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/backoffice/?action=source_add_save">
            <div>
                <label for="valeur">Nom de la source</label>
                <input type="text" id="valeur" name="valeur" maxlength="50" required>
            </div>

            <div>
                <button type="submit">Enregistrer</button>
            </div>
        </form>
    </main>
</body>
</html>
