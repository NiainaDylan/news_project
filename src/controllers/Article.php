<?php
declare(strict_types=1);

class Article
{
    private const UPLOAD_WEB_PREFIX = '/uploads/articles/';
    private const UPLOAD_ABS_DIR = '/var/www/html/uploads/articles';

    private static function normalizeLocalCachePath(string $rawValue): string
    {
        $rawValue = trim($rawValue);
        if ($rawValue === '') {
            return '';
        }

        if (str_starts_with($rawValue, self::UPLOAD_ABS_DIR . '/')) {
            return $rawValue;
        }

        $path = (string)(parse_url($rawValue, PHP_URL_PATH) ?? $rawValue);
        $path = preg_replace('#^(?:\./|\.\./)+#', '/', $path) ?? $path;
        $path = '/' . ltrim($path, '/');

        $prefixPos = strpos($path, self::UPLOAD_WEB_PREFIX);
        if ($prefixPos === false) {
            return '';
        }

        $fileName = basename(substr($path, $prefixPos + strlen(self::UPLOAD_WEB_PREFIX)));
        if ($fileName === '' || $fileName === '.' || $fileName === '..') {
            return '';
        }

        return self::UPLOAD_ABS_DIR . '/' . $fileName;
    }

    public static function list(): void
    {
        $stmt = getPDO()->query(
            'SELECT a.id,
                    a.valeur,
                    a.date_,
                    s.valeur AS source,
                    c.valeur AS categorie
             FROM article a
             LEFT JOIN source s ON s.id_source = a.id_source
             LEFT JOIN categorie_information c ON c.id_categorie = a.id_categorie
             ORDER BY a.date_ DESC, a.id DESC'
        );

        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../app/views/bo/article_list.php';
    }

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
        $dateCache   = trim((string)($_POST['date_cache'] ?? ''));
        $imagesRaw   = (string)($_POST['images_meta'] ?? '[]');

        if ($idCategorie <= 0 || $idSource <= 0 || $content === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Champs invalides']);
            return;
        }

        if ($dateCache !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateCache)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Date d\'expiration invalide']);
            return;
        }

        $imagesMeta = json_decode($imagesRaw, true);
        if (!is_array($imagesMeta)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Métadonnées images invalides']);
            return;
        }

        $pdo = getPDO();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO article (id_source, id_categorie, valeur)
                 VALUES (:id_source, :id_categorie, :valeur)'
            );
            $stmt->execute([
                ':id_source'    => $idSource,
                ':id_categorie' => $idCategorie,
                ':valeur'       => $content,
            ]);

            $articleId = (int)$pdo->lastInsertId();

            if (!empty($imagesMeta)) {
                $imageStmt = $pdo->prepare(
                    'INSERT INTO article_image (local_cache, alt, date_cache, id)
                     VALUES (:local_cache, :alt, :date_cache, :id)'
                );

                foreach ($imagesMeta as $image) {
                    if (!is_array($image)) {
                        continue;
                    }

                    $localCache = self::normalizeLocalCachePath((string)($image['local_cache'] ?? ''));
                    if ($localCache === '') {
                        continue;
                    }

                    $alt = trim((string)($image['alt'] ?? ''));

                    $imageStmt->execute([
                        ':local_cache' => $localCache,
                        ':alt'         => $alt,
                        ':date_cache'  => $dateCache !== '' ? $dateCache : null,
                        ':id'          => $articleId,
                    ]);
                }
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
            return;
        }

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

        $uploadDir = self::UPLOAD_ABS_DIR;
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
            'location' => self::UPLOAD_WEB_PREFIX . $fileName,
            'local_cache' => $absPath,
        ]);
    }

    public static function filterAjax(): void
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

        $idCategorieRaw = trim((string)($_POST['id_categorie'] ?? ''));
        $idSourceRaw    = trim((string)($_POST['id_source'] ?? ''));
        $statutRaw      = trim((string)($_POST['statut'] ?? ''));
        $limitInsertionRaw = trim((string)($_POST['limit_insertion'] ?? ''));

        $idCategorie = null;
        if ($idCategorieRaw !== '') {
            if (!ctype_digit($idCategorieRaw) || (int)$idCategorieRaw <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Catégorie invalide']);
                return;
            }
            $idCategorie = (int)$idCategorieRaw;
        }

        $idSource = null;
        if ($idSourceRaw !== '') {
            if (!ctype_digit($idSourceRaw) || (int)$idSourceRaw <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Source invalide']);
                return;
            }
            $idSource = (int)$idSourceRaw;
        }

        $statut = null;
        if ($statutRaw !== '') {
            if ($statutRaw !== '0' && $statutRaw !== '1') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Statut invalide']);
                return;
            }
            $statut = $statutRaw === '1';
        }

        if (!ctype_digit($limitInsertionRaw)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'limit_insertion invalide']);
            return;
        }

        $limitInsertion = (int)$limitInsertionRaw;
        if ($limitInsertion <= 0 || $limitInsertion > 200) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'limit_insertion hors limites']);
            return;
        }

        $query = 'SELECT a.id,
                         a.valeur,
                         a.date_,
                         a.statut,
                         s.valeur AS source,
                         c.valeur AS categorie
                  FROM article a
                  LEFT JOIN source s ON s.id_source = a.id_source
                  LEFT JOIN categorie_information c ON c.id_categorie = a.id_categorie';

        $where = ["a.date_ >= date_trunc('month', NOW()) AND a.date_ < (date_trunc('month', NOW()) + INTERVAL '1 month')"];
        $params = [];

        if ($idCategorie !== null) {
            $where[] = 'a.id_categorie = :id_categorie';
            $params[':id_categorie'] = $idCategorie;
        }

        if ($idSource !== null) {
            $where[] = 'a.id_source = :id_source';
            $params[':id_source'] = $idSource;
        }

        if ($statut !== null) {
            $where[] = 'a.statut = :statut';
            $params[':statut'] = $statut;
        }

        $query .= ' WHERE ' . implode(' AND ', $where);
        $query .= ' ORDER BY a.id DESC, a.date_ DESC LIMIT ' . $limitInsertion;

        try {
            $stmt = getPDO()->prepare($query);
            $stmt->execute($params);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors du filtrage']);
            return;
        }

        echo json_encode([
            'success' => true,
            'count' => count($articles),
            'filters_applied' => [
                'periode' => 'mois_courant_auto',
                'limit_insertion' => $limitInsertion,
                'id_categorie' => $idCategorie,
                'id_source' => $idSource,
                'statut' => $statut,
            ],
            'articles' => $articles,
        ]);
    }
}