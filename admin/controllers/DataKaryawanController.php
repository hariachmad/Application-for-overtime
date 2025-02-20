<?php 

require_once(__DIR__ . "/../models/DataKaryawanModel.php");

class DataKaryawanController
{

    public function index()
    {
        $dataKaryawanModel = new DataKaryawanModel();
        $result = $dataKaryawanModel->findAll();
        
        require realpath(dirname(__FILE__) . "../../views/DataKaryawan.php");
    }

}

?>