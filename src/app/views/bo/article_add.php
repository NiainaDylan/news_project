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
            <label for="id_categorie">Catégorie</label>
            <select id="id_categorie" name="id_categorie" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <option value="<?= (int)$cat['id_categorie'] ?>">
                        <?= e($cat['valeur']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="id_source">Source</label>
            <select id="id_source" name="id_source" required>
                <option value="">-- Choisir une source --</option>
                <?php foreach (($sources ?? []) as $src): ?>
                    <option value="<?= (int)$src['id_source'] ?>">
                        <?= e($src['valeur']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="article_content">Contenu</label>
            <textarea id="article_content" name="content"></textarea>
        </div>

        <div>
            <label for="date_cache">Expiration du cache image</label>
            <input id="date_cache" name="date_cache" type="date">
        </div>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>