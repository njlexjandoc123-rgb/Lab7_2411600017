<?php

namespace App\Exports;

use Illuminate\Support\Collection;

interface ReportExporter
{
    /**
     * @return array<int, string>
     */
    public function headings(): array;

    /**
     * @param array<string, string> $filters
     * @return Collection<int, array<int, string|int|float|null>>
     */
    public function rows(array $filters): Collection;
}
