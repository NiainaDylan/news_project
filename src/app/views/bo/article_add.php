<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article</title>

    <script src="https://cdn.tiny.cloud/1/1s8nz8ancbmnlufy7x0nan5i9tn4nuv5ut3mzm7xqlhrd6ea/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    <script src="/backoffice/assets/js/tinymce.js"></script>
</head>
<body>
    <?= renderBackofficeNav('articles') ?>

    <h1>Ajouter un article</h1>

    <form method="post" action="/backoffice/?action=article_add_save">
        <div>
            <label for="article_content">Contenu</label>
            <textarea id="article_content" name="content">Welcome to TinyMCE!</textarea>
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>