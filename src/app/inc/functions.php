<?php
declare(strict_types=1);

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>