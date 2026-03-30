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

function renderBackofficeNav(string $current = 'home'): string
{
    $nav = [
        'home'  => ['label' => 'Home', 'url' => '/backoffice/?action=home'],
        'articles'   => [
            'label' => 'Articles',
            'children' => [
                ['label' => 'Liste', 'url' => '/backoffice/?action=article_list'],
                ['label' => 'Ajouter', 'url' => '/backoffice/?action=article_add'],
            ],
        ],
        'categories' => [
            'label' => 'Catégories',
            'children' => [
                ['label' => 'Liste', 'url' => '/backoffice/?action=categorie_list'],
                ['label' => 'Ajouter', 'url' => '/backoffice/?action=categorie_add'],
            ],
        ],
        'sources'    => [
            'label' => 'Sources',
            'children' => [
                ['label' => 'Liste', 'url' => '/backoffice/?action=source_list'],
                ['label' => 'Ajouter', 'url' => '/backoffice/?action=source_add'],
            ],
        ],
    ];

    $html = '<nav><ul>';

    foreach ($nav as $key => $item) {
        if (isset($item['children'])) {
            $html .= '<li>';
            $html .= '<details' . ($current === $key ? ' open' : '') . '>';
            $html .= '<summary>' . e($item['label']) . '</summary>';
            $html .= '<ul>';

            foreach ($item['children'] as $child) {
                $html .= '<li><a href="' . e($child['url']) . '">' . e($child['label']) . '</a></li>';
            }

            $html .= '</ul>';
            $html .= '</details>';
            $html .= '</li>';
            continue;
        }

        $html .= '<li><a href="' . e($item['url']) . '">' . e($item['label']) . '</a></li>';
    }

    $html .= '<li><a href="/backoffice/?action=logout">Déconnexion</a></li>';
    $html .= '</ul></nav>';

    return $html;
}

?>