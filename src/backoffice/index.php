<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../app/inc/db.php';
require_once __DIR__ . '/../app/inc/functions.php';

require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/Categorie.php';
require_once __DIR__ . '/../models/Source.php';
require_once __DIR__ . '/../controllers/Auth.php';
require_once __DIR__ . '/../controllers/Article.php';

$action = $_GET['action'] ?? 'home';
$public = ['login'];

if (!isset($_SESSION['admin']) && !in_array($action, $public)) {
    redirect('/backoffice/?action=login');
}

match($action) {
    'login'     => Auth::login(),
    'logout'    => Auth::logout(),
    'home'      => require __DIR__ . '/../app/views/bo/home.php',
    'article_add' => (function () {
        $categories = Categorie::findAll();
        $sources    = Source::findAll();
        require __DIR__ . '/../app/views/bo/article_add.php';
    })(),
    'article_add_save' => Article::saveAjax(),
    'article_image_upload' => Article::uploadImageAjax(),
    default     => redirect('/backoffice/?action=home'),
};