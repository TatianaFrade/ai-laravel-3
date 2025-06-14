<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class UserSpendingExport implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
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
                'Date' => $row->data,
                'Total Spent (€)' => $row->total,
            ];
        });
    }

    public function headings(): array
    {
        return ['Date', 'Total Spent (€)'];
    }
}
