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
require_once __DIR__ . '/../controllers/CategorieController.php';
require_once __DIR__ . '/../controllers/SourceController.php';

$action = $_GET['action'] ?? 'home';
$public = ['login'];

if (!isset($_SESSION['admin']) && !in_array($action, $public)) {
    redirect('/backoffice/?action=login');
}

match($action) {
    'login'     => Auth::login(),
    'logout'    => Auth::logout(),
    'home'      => require __DIR__ . '/../app/views/bo/home.php',
    'article_list' => Article::list(),
    'article_detail' => Article::detail(),
    'article_add' => Article::form(),
    'article_add_save' => Article::saveAjax(),
    'article_image_upload' => Article::uploadImageAjax(),
    'article_filter' => Article::filterAjax(),
    'categorie_list' => CategorieController::list(),
    'categorie_add' => CategorieController::form(),
    'categorie_add_save' => CategorieController::save(),
    'source_list' => SourceController::list(),
    'source_add' => SourceController::form(),
    'source_add_save' => SourceController::save(),
    default     => redirect('/backoffice/?action=home'),
};