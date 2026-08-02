<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class OrdersExport extends DefaultValueBinder implements FromArray, WithCustomValueBinder, WithHeadings, WithEvents
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['訂單號', '内單號', '商品', '總價', '名字', '電話', '郵箱', '地址', '收貨方式', '配送時間', '備注', '訂單狀態'];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && $cell->getColumn() !== 'D') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $highestRow)
                    ->getAlignment()
                    ->setVertical('center')
                    ->setHorizontal('center');

                $sheet->getStyle('C2:C' . $highestRow)
                    ->getAlignment()
                    ->setWrapText(true);

                $widths = ['A' => 25, 'B' => 25, 'C' => 30, 'D' => 15, 'E' => 12, 'F' => 15, 'G' => 25, 'H' => 55, 'I' => 15, 'J' => 15, 'K' => 25, 'L' => 12];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $sheet->getStyle('A1:L1')->getFont()->setBold(true);
            },
        ];
    }
}