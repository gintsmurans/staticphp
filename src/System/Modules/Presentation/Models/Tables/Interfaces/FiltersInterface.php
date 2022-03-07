<?php

namespace System\Modules\Presentation\Models\Tables\Interfaces;

/**
 * Filters Interface
 */
interface FiltersInterface
{
    public function __construct(TableInterface &$tableInstance, string $urlPrefix = '', ?string $filterData = null);

    public function url(): ?string;
    public function setUrl(?string $setUrl = null): void;

    public function filterData(): string;

    public function parsedData(): array;
    public function hasFilter(string $key): bool;
    public function filterValue(string $key);

    public function parse(?string $filterData = null, ?\Closure $callback = null): void;
}
