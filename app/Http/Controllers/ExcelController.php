<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use Illuminate\Http\Request;
use App\Imports\AddressImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController
{
    public function handleImportExport(Request $request)
    {
        $file1 = $request->file('file1');
        $file2 = $request->file('file2');
        // 导入 Excel 表为数组
        $import     = new AddressImport();
        $streetData = Excel::toArray($import, $file1);
        if ($streetData) {
            unset($streetData[0][0]);
        }
        $addressData = Excel::toArray($import, $file2);
        if ($addressData) {
            unset($addressData[0][0]);
        }

        $returnData = [];

        foreach ($streetData[0] as $street) {
            foreach ($addressData[0] as $key => $address) {
                if (strpos($address[1], $street[2]) !== false || strpos($address[2], $street[2]) !== false) {
                    // 设备个数
                    if (isset($returnData[$street[2]])) {
                        $number = $returnData[$street[2]]['number'] ?? 0;
                    } else {
                        $number = 0;
                    }

                    $returnData[$street[2]]['number'] = $number + (int) $address[5];
                    $returnData[$street[2]]['street'] = $street[2];

                    $addressDetailValue                       = $returnData[$street[2]]['address_detail'] ?? '=';
                    $returnData[$street[2]]['address_detail'] = ($addressDetailValue === '=') ? $addressDetailValue . '"' . $address[2] . '"' : $addressDetailValue . '&char(10)&' . '"' . $address[2] . '"';

                    $nameValue                      = $returnData[$street[2]]['name'] ?? '=';
                    $returnData[$street[2]]['name'] = ($nameValue === '=') ? $nameValue . '"' . $address[4] . '"' : $nameValue . '&char(10)&' . '"' . $address[4] . '"';

                    $phoneValue                      = $returnData[$street[2]]['phone'] ?? '=';
                    $returnData[$street[2]]['phone'] = ($phoneValue === '=') ? $phoneValue . '"' . $address[3] . '"' : $phoneValue . '&char(10)&' . '"' . $address[3] . '"';

                    unset($addressData[0][$key]);
                }
            }
        }

        // 创建导出类
        $export = new DataExport(array_values($returnData));

        return Excel::download($export, 'processed_data.xlsx');
    }
}
