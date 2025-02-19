<?php

require_once(__DIR__ . "/../models/ListPengajuanModel.php");
require_once(__DIR__ . "/../../repository/databaseService.php");

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

}
?>