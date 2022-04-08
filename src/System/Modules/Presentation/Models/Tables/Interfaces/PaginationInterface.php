<?php

namespace System\Modules\Presentation\Models\Tables\Interfaces;

/**
 * Pagination Interface
 */
interface PaginationInterface
{
    public function __construct(
        TableInterface &$tableInstance,
        string $urlPrefix = '',
        int $currentPage = 1,
        int $limitPerPage = 50,
        int $pagesToShow = 10
    );
    public function url(): string;
    public function calculate(int $recordCount, int $currentPage): void;
}
