<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class SalesByCategoryExport implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($row) {
            return [
                'Date'    => $row->data,
                'Category' => $row->category,
                'Total'    => $row->totalS,
            ];
        });
    }

    public function headings(): array
    {
        return ['Date', 'Category', 'Total (€)'];
    }
}
