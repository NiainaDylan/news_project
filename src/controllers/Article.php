<?php
declare(strict_types=1);

class Article
{
    public static function saveAjax(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isPost()) {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Méthode non autorisée',
            ]);
            return;
        }

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!$isAjax) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Requête AJAX attendue',
            ]);
            return;
        }

        $idCategorie = (int)($_POST['id_categorie'] ?? 0);
        $idSource    = (int)($_POST['id_source'] ?? 0);
        $content     = trim($_POST['content'] ?? '');

        if ($idCategorie <= 0 || $idSource <= 0 || $content === '') {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Champs invalides',
            ]);
            return;
        }

        $stmt = getPDO()->prepare(
            'INSERT INTO article (id_source, id_categorie, valeur)
             VALUES (:id_source, :id_categorie, :valeur)'
        );

        $stmt->execute([
            ':id_source'    => $idSource,
            ':id_categorie' => $idCategorie,
            ':valeur'       => $content,
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Article enregistré',
        ]);
    }
}