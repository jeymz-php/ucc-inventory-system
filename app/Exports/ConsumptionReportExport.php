<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ConsumptionReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return ['Date', 'Reference No.', 'Item', 'Category', 'Quantity', 'Unit', 'Department', 'Recipient', 'Purpose'];
    }

    public function map($item): array
    {
        return [
            $item->request->request_date->format('M d, Y'),
            $item->request->reference_no,
            $item->consumable->item_name ?? '—',
            $item->consumable->category->name ?? 'Uncategorized',
            $item->quantity,
            $item->consumable->unit ?? '—',
            $item->request->department,
            $item->request->recipient_name,
            $item->purpose ?? '—',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A6B3A']],
            ],
        ];
    }
}