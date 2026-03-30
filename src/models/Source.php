<?php 
declare(strict_types=1);

class Source
{
    public static function findAll(): array
    {
        $stmt = getPDO()->query("SELECT * FROM source ORDER BY valeur");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(string $valeur): void
    {
        $stmt = getPDO()->prepare(
            "INSERT INTO source (valeur) VALUES (:valeur)"
        );
        $stmt->execute([':valeur' => $valeur]);
    }
}