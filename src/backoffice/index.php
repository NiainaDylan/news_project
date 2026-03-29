<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/inc/db.php';
require_once __DIR__ . '/../app/inc/functions.php';

require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../controllers/Auth.php';

$action = $_GET['action'] ?? 'home';
$public = ['login'];

if (!isset($_SESSION['admin']) && !in_array($action, $public)) {
    redirect('/backoffice/?action=login');
}

match($action) {
    'login'     => Auth::login(),
    'logout'    => Auth::logout(),
    'home'      => require __DIR__ . '/../views/bo/home.php',
    default     => redirect('/backoffice/?action=home'),
};