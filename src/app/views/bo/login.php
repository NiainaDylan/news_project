<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Backoffice</title>
    <link rel="stylesheet" href="/backoffice/assets/css/bo.css">
</head>
<body class="login-page">
    <div class="login-card">
        <h1>Backoffice</h1>
        <p class="login-sub">Site d'informations — Iran</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/backoffice/?action=login">
            <div class="field">
                <label for="login">Identifiant</label>
                <input type="text" id="login" name="login"
                       autocomplete="username" required autofocus>
            </div>
            <div class="field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"
                       autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
    </div>
</body>
</html>