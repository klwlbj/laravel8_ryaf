<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class DataExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        // 设置表头
        return ['安装烟感总数', '楼栋地址',  '安装明细（细分到商铺或者房间号）', '姓名', '电话'];
    }

    public function map($row): array
    {
        // 处理每一行数据
        return [
            $row['number'],
            $row['street'],
            $row['address_detail'],
            $row['name'],
            $row['phone'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // 设置单元格自动换行
                $event->sheet->getDelegate()->getStyle('C')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('D')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('E')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
