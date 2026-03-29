<?php
declare(strict_types=1);

class Auth
{
    public static function login(): void
    {
        $error = null;

        if (isPost()) {
            $login    = trim($_POST['login'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $admin    = AdminModel::findByLogin($login);

            if ($admin && $password === $admin['password']) {
                $_SESSION['admin'] = [
                    'id'    => $admin['id'],
                    'login' => $admin['login'],
                ];
                redirect('/backoffice/?action=home');
            }

            $error = 'Identifiants incorrects.';
        }

        require __DIR__ . '/../app/views/bo/login.php';
    }

    public static function logout(): void
    {
        session_destroy();
        redirect('/backoffice/?action=login');
    }
}