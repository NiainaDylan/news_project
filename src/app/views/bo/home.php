<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <h1>Bienvenue <?= e($_SESSION['admin']['login']) ?></h1>
    <?= renderBackofficeNav('home') ?>
    <a href="/backoffice/?action=logout">Déconnexion</a>
</body>
</html>