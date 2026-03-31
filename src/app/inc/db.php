<?php
declare(strict_types=1);

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = 'db';        
        $port = '5432';
        $name = 'news_db';
        $user = 'news_user';
        $pass = 'password';

        $pdo = new PDO(
            "pgsql:host={$host};port={$port};dbname={$name}",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    return $pdo;
}