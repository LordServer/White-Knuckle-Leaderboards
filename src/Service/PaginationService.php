<?php

namespace App\Service;

class PaginationService
{
    public function build(
        int $currentPage,
        int $totalPages,
        int $radius = 2,
        int $totalResults = 0,
        int $maxPerPage = 50,
    ): array {
        if ($totalPages <= 1) {
            return [
                'current' => $currentPage,
                'total' => $totalPages,
                'totalResults' => $totalResults,
                'maxPerPage' => $maxPerPage,
                'items' => [1],
                'hasPrevious' => false,
                'hasNext' => false,
                'previousPage' => null,
                'nextPage' => null,
            ];
        }

        $items = [];

        // First page
        $items[] = 1;

        $start = max(2, $currentPage - $radius);
        $end = min($totalPages - 1, $currentPage + $radius);

        // Left ellipsis
        if ($start > 2) {
            $items[] = '...';
        }

        // Visible page window
        for ($page = $start; $page <= $end; ++$page) {
            $items[] = $page;
        }

        // Right ellipsis
        if ($end < $totalPages - 1) {
            $items[] = '...';
        }

        // Last page
        if ($totalPages > 1) {
            $items[] = $totalPages;
        }

        return [
            'current' => $currentPage,
            'total' => $totalPages,
            'totalResults' => $totalResults,
            'maxPerPage' => $maxPerPage,
            'items' => $items,
            'hasPrevious' => $currentPage > 1,
            'hasNext' => $currentPage < $totalPages,
            'previousPage' => $currentPage > 1
                ? $currentPage - 1
                : null,
            'nextPage' => $currentPage < $totalPages
                ? $currentPage + 1
                : null,
        ];
    }
}
