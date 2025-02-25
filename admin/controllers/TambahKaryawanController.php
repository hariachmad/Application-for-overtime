<?php

require_once(__DIR__ . "/../models/DataKaryawanModel.php");
class TambahKaryawanController
{
    public function __construct()
    {
    }

    public function index()
    {
        include realpath(dirname(__FILE__) . "../../views/TambahKaryawan.php");
    }

    public function createKaryawan($username, $password, $divisi): bool
    {
        $karyawan = new DataKaryawanModel();
        $result = $karyawan->createUser($username, $password, $divisi);
        if ($result) {
            return true;
        }
        return false;

    }
}

?>