<?php

require_once(__DIR__."/../models/PengajuanLemburModel.php");
require_once(__DIR__."/../../repository/databaseService.php");

class PengajuanLemburController
{
    private $conn;

    public function __construct()
    {
        $db = new DatabaseService();
        $this->conn = $db->getConn();
    }

    public function index()
    {
        // $pengajuanLemburModel = new PengajuanLemburModel();
        // $pegajuanLembur = $userModel->getAllUsers();
        $pengajuanLemburModel = new PengajuanLemburModel();
        $result = $pengajuanLemburModel->getRows();

        include realpath(dirname(__FILE__)."../../views/PengajuanLembur.php");
    }

    public function approve($id,$admin_id,$admin_role): bool{
        $pengajuanLemburModel = new PengajuanLemburModel();
        $result = $pengajuanLemburModel->approve($id,$admin_id,$admin_role);
        return $result;
    }

    public function reject($id,$admin_id,$admin_role): bool{
        $pengajuanLemburModel = new PengajuanLemburModel();
        $result = $pengajuanLemburModel->reject($id,$admin_id,$admin_role);
        return $result;
    }
}
?>