<?php

namespace System\Modules\Presentation\Models\Tables\Interfaces;

/**
 * Table Interface
 */
interface TableInterface
{
    public function __construct(array $columns, string $urlPrefix = '');

    public function tableId(): string;
    public function parseQueryString(string $str, string $delimiter = '&');

    public function initData(?string $filterData = null, ?string $sortData = null, ?int $page = null): void;

    public function getColumns(): array;
    public function setColumns(array $columns): void;

    public function getRows(): array;
    public function setRows(array &$rows): void;

    public function makeOutput();
    public function showOutput(): void;
}
