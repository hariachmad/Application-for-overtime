<?php

require_once(__DIR__ . "/../../vendor/autoload.php");
require_once(__DIR__ . "/../models/ListPengajuanModel.php");

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxService
{
    protected $spreadsheet;
    protected $listPengajuanModel;
    public function __construct($headers)
    {
        $this->listPengajuanModel = new ListPengajuanModel();
        $this->spreadsheet = new Spreadsheet();
        $result = $this->listPengajuanModel->getRows();

        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(22);
        $sheet->getColumnDimension('K')->setWidth(22);
        $sheet->getColumnDimension('L')->setWidth(22);
        $sheet->getColumnDimension('M')->setWidth(22);
        $sheet->getColumnDimension('N')->setWidth(22);
        $headersAfterSplit = explode(",", $headers);

        foreach ($headersAfterSplit as $index => $header) {
            $sheet->setCellValue([$index + 1, 1], $header);
        }

        if ($result["pengajuanLembur"]->num_rows > 0) {
            $rowNumber = 2;
            $fotoPengajuan = $result["fotoPengajuanBefore"]->fetch_all(MYSQLI_ASSOC);
            while ($row = $result["pengajuanLembur"]->fetch_assoc()) {
                $sheet->setCellValue([1, $rowNumber], $row['nama_karyawan']);
                $sheet->setCellValue([2, $rowNumber], $row['jenis_proyek']);
                $sheet->setCellValue([3, $rowNumber], $row['nama_proyek']);
                $sheet->setCellValue([4, $rowNumber], date('d/m/Y', strtotime($row['tanggal_lembur'])));
                $sheet->setCellValue([5, $rowNumber], $row['jam_mulai'] . $row['jam_selesai']);
                $sheet->setCellValue([6, $rowNumber], $row['daftar_pekerjaan']);
                $sheet->setCellValue([7, $rowNumber], $row['status_pengajuan']);
                $sheet->setCellValue([8, $rowNumber], isset($row['approver_role']) ? $row['approver_role'] : '-');

                $colsFotoSebelum = ['I', 'J', 'K', 'L', 'M'];
                $indexColsFotoSebelum = 0;
                foreach($fotoPengajuan as $rowFoto){
                    if ($row["pengajuan_id"] == $rowFoto["pengajuan_id"]) {
                        $drawing = new Drawing();
                        $drawing->setName('Foto');
                        $drawing->setDescription('Foto');
                        $drawing->setPath('user/' . $rowFoto["path"]);
                        $drawing->setCoordinates($colsFotoSebelum[$indexColsFotoSebelum] . $rowNumber);
                        $drawing->setWidth(10);
                        $drawing->setHeight(20);
                        $drawing->setWorksheet($sheet);
                        $indexColsFotoSebelum = $indexColsFotoSebelum + 1;
                    }
                }
                $rowNumber = $rowNumber + 1;
                // while ($rowFoto = $result["fotoPengajuanBefore"]->fetch_assoc()) {
                    
                // }

                // $colsFotoSesudah = ['N', 'O', 'P', 'Q', 'R'];
                // $indexColsFotoSesudah = 0;
                // while ($row = $result["fotoPengajuanAfter"]->fetch_assoc()) {
                //     $drawing = new Drawing();
                //     $drawing->setName('Foto');
                //     $drawing->setDescription('Foto');
                //     $drawing->setPath('user/' . $row["path"]);
                //     $drawing->setCoordinates($colsFotoSesudah[$indexColsFotoSesudah] . $rowNumber);
                //     $drawing->setWidth(10);
                //     $drawing->setHeight(10);
                //     $drawing->setWorksheet($sheet);
                //     $indexColsFotoSesudah = $indexColsFotoSesudah + 1;
                // }

            }
        }

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('example.xlsx');

        var_dump("File Excel Berhasil Dibuat");
        exit();
    }
}
?>