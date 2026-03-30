<?php
declare(strict_types=1);

class AdminModel
{
    public static function findByLogin(string $login): ?array
    {
        $stmt = getPDO()->prepare(
            "SELECT * FROM admin WHERE login = :login LIMIT 1"
        );
        $stmt->execute([':login' => $login]);
        return $stmt->fetch() ?: null;
    }
}