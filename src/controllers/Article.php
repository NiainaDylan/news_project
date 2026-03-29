<?php
declare(strict_types=1);

class Article
{
    public static function saveAjax(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isPost()) {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!$isAjax) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Requête AJAX attendue']);
            return;
        }

        $idCategorie = (int)($_POST['id_categorie'] ?? 0);
        $idSource    = (int)($_POST['id_source']    ?? 0);
        $content     = trim($_POST['content']       ?? '');

        if ($idCategorie <= 0 || $idSource <= 0 || $content === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Champs invalides']);
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

        echo json_encode(['success' => true, 'message' => 'Article enregistré']);
    }

    public static function uploadImageAjax(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isPost()) {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }

        $isAjaxHeader = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $acceptJson   = isset($_SERVER['HTTP_ACCEPT'])
            && str_contains(strtolower((string)$_SERVER['HTTP_ACCEPT']), 'application/json');

        if (!$isAjaxHeader && !$acceptJson) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Requête AJAX attendue']);
            return;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Fichier invalide']);
            return;
        }

        $apiKey = (string)getenv('TINIFY_API_KEY');
        if ($apiKey === '') {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'TINIFY_API_KEY manquante']);
            return;
        }

        $tmpPath      = $_FILES['file']['tmp_name'];
        $originalName = (string)($_FILES['file']['name'] ?? 'image.jpg');
        $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $ext = 'jpg';
        }

        // Dimensions envoyées par TinyMCE
        $width  = (int)($_POST['width']  ?? 0);
        $height = (int)($_POST['height'] ?? 0);

        $binary = file_get_contents($tmpPath);
        if ($binary === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lecture fichier impossible']);
            return;
        }

        // Étape 1 : compression initiale (shrink)
        $ch = curl_init('https://api.tinify.com/shrink');
        curl_setopt_array($ch, [
            CURLOPT_USERPWD        => 'api:' . $apiKey,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $binary,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            curl_close($ch);
            http_response_code(502);
            echo json_encode(['success' => false, 'message' => 'Erreur Tinify (shrink)']);
            return;
        }

        $status     = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $headersRaw = substr($response, 0, $headerSize);
        if ($status < 200 || $status >= 300) {
            http_response_code(502);
            echo json_encode(['success' => false, 'message' => 'Tinify a refusé le fichier']);
            return;
        }

        $location = null;
        foreach (explode("\r\n", $headersRaw) as $line) {
            if (stripos($line, 'Location:') === 0) {
                $location = trim(substr($line, 9));
                break;
            }
        }

        if (!$location) {
            http_response_code(502);
            echo json_encode(['success' => false, 'message' => 'Réponse Tinify invalide']);
            return;
        }

        // Étape 2 : resize + téléchargement de l'image optimisée
        $resizeOptions = ['method' => 'fit'];
        if ($width  > 0) $resizeOptions['width']  = $width;
        if ($height > 0) $resizeOptions['height'] = $height;

        $ch2 = curl_init($location);
        curl_setopt_array($ch2, [
            CURLOPT_USERPWD        => 'api:' . $apiKey,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['resize' => $resizeOptions]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $optimized = curl_exec($ch2);
        $status2   = (int)curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);

        if ($optimized === false || $status2 < 200 || $status2 >= 300) {
            http_response_code(502);
            echo json_encode(['success' => false, 'message' => 'Téléchargement image optimisée impossible']);
            return;
        }

        $uploadDir = '/var/www/html/uploads/articles';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Création dossier uploads impossible']);
            return;
        }

        $fileName = 'img_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $absPath  = $uploadDir . '/' . $fileName;

        if (file_put_contents($absPath, $optimized) === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Écriture image impossible']);
            return;
        }

        echo json_encode([
            'success'  => true,
            'location' => '/uploads/articles/' . $fileName,
        ]);
    }
}