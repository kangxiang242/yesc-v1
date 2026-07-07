<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class OrderXlsxExport extends DefaultValueBinder implements FromArray, WithCustomValueBinder, WithEvents
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

    /**
     * 將數字欄位（除了總價/金額）強制轉為文字，避免 Excel 顯示科學記號
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        $column = $cell->getColumn();

        // D 欄 = 總價（保持數值以便計算），其他數字欄位強制文字
        if (is_numeric($value) && $column !== 'D') {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $maxRow = count($this->data); // 含標題行

                // 垂直 + 水平居中
                $styleRange = "A1:L{$maxRow}";
                $sheet->getStyle($styleRange)->getAlignment()->setVertical('center');
                $sheet->getStyle($styleRange)->getAlignment()->setHorizontal('center');

                // 商品欄（C）啟用自動換行
                $sheet->getStyle("C2:C{$maxRow}")->getAlignment()->setWrapText(true);

                // 地址欄（H）啟用自動換行
                $sheet->getStyle("H2:H{$maxRow}")->getAlignment()->setWrapText(true);

                // 列寬設定
                $sheet->getColumnDimension('A')->setWidth(25);  // 訂單號
                $sheet->getColumnDimension('B')->setWidth(25);  // 内單號
                $sheet->getColumnDimension('C')->setWidth(35);  // 商品
                $sheet->getColumnDimension('D')->setWidth(15);  // 總價
                $sheet->getColumnDimension('E')->setWidth(12);  // 名字
                $sheet->getColumnDimension('F')->setWidth(15);  // 電話
                $sheet->getColumnDimension('G')->setWidth(25);  // 郵箱
                $sheet->getColumnDimension('H')->setWidth(60);  // 地址
                $sheet->getColumnDimension('I')->setWidth(15);  // 收貨方式
                $sheet->getColumnDimension('J')->setWidth(20);  // 配送時間
                $sheet->getColumnDimension('K')->setWidth(30);  // 備注
                $sheet->getColumnDimension('L')->setWidth(15);  // 訂單狀態

                // 標題行加粗
                $sheet->getStyle("A1:L1")->getFont()->setBold(true);
            },
        ];
    }
}