<?php


require_once realpath(dirname(__FILE__) . "../../../repository/databaseService.php");
class DataKaryawanModel
{
    private $conn;

    public function __construct()
    {
        // $this->conn = new DatabaseService()->getConn();
        $databaseService = new DatabaseService();
        $this->conn = $databaseService->getConn();
    }

    public function findAll(){
        $query = "select * from karyawan";
        $result = $this->conn->query($query);

        if (!$result) {
            die("Error fetching requests: " . $this->conn->error);
        }

        return $result;
    }
}

?>