<?php
declare(strict_types=1);

class SourceController
{
    public static function list(): void
    {
        $sources = Source::findAll();
        require __DIR__ . '/../app/views/bo/source_list.php';
    }

    public static function form(): void
    {
        $error = null;
        require __DIR__ . '/../app/views/bo/source_add.php';
    }

    public static function save(): void
    {
        if (!isPost()) {
            redirect('/backoffice/?action=source_add');
        }

        $valeur = trim((string)($_POST['valeur'] ?? ''));
        $error = null;

        if ($valeur === '') {
            $error = 'La source est obligatoire.';
        } elseif (mb_strlen($valeur, 'UTF-8') > 50) {
            $error = 'La source ne doit pas depasser 50 caracteres.';
        }

        if ($error !== null) {
            require __DIR__ . '/../app/views/bo/source_add.php';
            return;
        }

        try {
            Source::create($valeur);
            redirect('/backoffice/?action=source_list&created=1');
        } catch (Throwable $e) {
            $error = 'Erreur lors de l enregistrement.';
            require __DIR__ . '/../app/views/bo/source_add.php';
        }
    }
}
