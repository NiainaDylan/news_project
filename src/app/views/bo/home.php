<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accueil — Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="has-nav">
    <?= renderBackofficeNav('home') ?>

    <main class="main-content">
        <h1>Bienvenue, <?= e($_SESSION['admin']['login']) ?></h1>
        <p>Sélectionnez une section dans le menu pour commencer.</p>
    </main>
</body>
</html>