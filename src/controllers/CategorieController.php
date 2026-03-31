<?php
declare(strict_types=1);

class CategorieController
{
    public static function list(): void
    {
        $categories = Categorie::findAll();
        require __DIR__ . '/../app/views/bo/categorie_list.php';
    }

    public static function form(): void
    {
        $error = null;
        require __DIR__ . '/../app/views/bo/categorie_add.php';
    }

    public static function save(): void
    {
        if (!isPost()) {
            redirect('/backoffice/?action=categorie_add');
        }

        $valeur = trim((string)($_POST['valeur'] ?? ''));
        $error = null;

        if ($valeur === '') {
            $error = 'La categorie est obligatoire.';
        } elseif (mb_strlen($valeur, 'UTF-8') > 50) {
            $error = 'La categorie ne doit pas depasser 50 caracteres.';
        }

        if ($error !== null) {
            require __DIR__ . '/../app/views/bo/categorie_add.php';
            return;
        }

        try {
            Categorie::create($valeur);
            redirect('/backoffice/?action=categorie_list&created=1');
        } catch (Throwable $e) {
            $error = 'Erreur lors de l enregistrement.';
            require __DIR__ . '/../app/views/bo/categorie_add.php';
        }
    }
}
