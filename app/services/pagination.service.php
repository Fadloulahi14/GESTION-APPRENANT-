<?php

function paginer(array $items, int $pageCourante, int $parPage): array {
    $totalItems = count($items);
    $totalPages = (int) ceil($totalItems / $parPage);
    $offset = ($pageCourante - 1) * $parPage;

    return [
        'items' => array_slice($items, $offset, $parPage),
        'pageCourante' => $pageCourante,
        'pages' => $totalPages,
        'totalItems' => $totalItems
    ];
}