<?php 
declare(strict_types=1);

class Categorie
{
    public static function findAll(): array
    {
        $stmt = getPDO()->query("SELECT * FROM categorie_information ORDER BY valeur");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(string $valeur): void
    {
        $stmt = getPDO()->prepare(
            "INSERT INTO categorie_information (valeur) VALUES (:valeur)"
        );
        $stmt->execute([':valeur' => $valeur]);
    }
}