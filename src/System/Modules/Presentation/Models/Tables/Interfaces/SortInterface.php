<?php

namespace System\Modules\Presentation\Models\Tables\Interfaces;

use System\Modules\Presentation\Models\Tables\Enums\SortDirection;

/**
 * Sort Interface
 */
interface SortInterface
{
    public function __construct(TableInterface &$tableInstance, string $urlPrefix = '', ?string $sortData = null);

    public function url(): string;
    public function setUrl(?string $setUrl = null): void;

    public function currentColumn(): ?ColumnInterface;
    public function currentDirection(): SortDirection;

    public function parse(string $sortData): void;

    public function sortData(): string;

    public function sortBy(): string;
    public function sortDirection(): SortDirection;
}
