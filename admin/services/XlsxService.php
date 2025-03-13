<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxService
{
    protected $spreadsheet;
    public function __construct($headers,$data)
    {
        $this->spreadsheet = new Spreadsheet();
        $sheet = $this->spreadsheet->getActiveSheet();
        $headersAfterSplit= explode(",",$headers);

        foreach ($headersAfterSplit as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('example.xlsx');

        var_dump("File Excel Berhasil Dibuat");
        exit();
    }
}




?>