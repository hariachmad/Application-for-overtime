<?php

require_once(__DIR__ . "/../models/ListPengajuanModel.php");
require_once(__DIR__ . "/../../repository/databaseService.php");
require_once(__DIR__ . "/../services/XlsxService.php");

class ListPengajuanController
{
    private $conn;

    public function __construct()
    {
        $db = new DatabaseService();
        $this->conn = $db->getConn();
    }

    public function index()
    {
        $listPengajuanModel = new ListPengajuanModel();
        $result = $listPengajuanModel->getRows();

        include realpath(dirname(__FILE__) . "../../views/ListPengajuan.php");
    }

    public function generate($headers)
    {
        $xlsxService = new XlsxService($headers);

    }

    public function create($pengajuan)
    {
        $listPengajuanModel = new ListPengajuanModel();
        $result = $listPengajuanModel->create($pengajuan);
        return $result;
    }
}
?>