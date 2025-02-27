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

    public function delete($id)
    {
        $dataKaryawanModel = new DataKaryawanModel();
        $result = $dataKaryawanModel->deleteUser($id);
        if($result){
            require realpath(dirname(__FILE__) . "../../views/DataKaryawan.php");
            exit();
        }
            echo "Gagal Hapus Data";
    }

    public function update($id,$nama,$divisi){
        $dataKaryawanModel = new DataKaryawanModel();
        $result = $dataKaryawanModel->updateUser($id,$nama,$divisi);
        if($result){
            require realpath(dirname(__FILE__) . "../../views/DataKaryawan.php");
            exit();
        }
        echo "Gagal Hapus Data";
    }

}

?>