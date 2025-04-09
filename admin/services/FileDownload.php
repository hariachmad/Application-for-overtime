<?php
class FileDownload
{

    public static function DownloadXlsxFile()
    {

        $file_path = 'report/report.xlsx';

        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Cache-Control: public');
            header('Expires: 0');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Transfer-Encoding: binary');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file_path);
            exit;
        } else {
            // File tidak ditemukan
            http_response_code(404);
            die('File not found');
        }
    }


}
?>