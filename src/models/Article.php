<?php 
declare(strict_types=1);

class Article
{
    public static function findAll(): array
    {
            $stmt = getPDO()->query("SELECT * FROM source ORDER BY valeur");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}