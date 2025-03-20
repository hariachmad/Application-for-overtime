<?php

require_once realpath(dirname(__FILE__) . "../../../repository/databaseService.php");

class FotoPengajuan
{
    private $conn;

    public function __construct()
    {
        // $this->conn = new DatabaseService()->getConn();
        $databaseService = new DatabaseService();
        $this->conn = $databaseService->getConn();
    }

    public function getCurrentId()
    {
        $query = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'blue_lake' AND TABLE_NAME = 'pengajuan_lembur'";
        $result = $this->conn->query($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $autoIncrement = $row['AUTO_INCREMENT'];
            return $autoIncrement;
        } else {
            throw new Exception('error get Auto Increment');
        }
    }

    public function create($pengajuan)
    {
        $idPengajuanLembur = $pengajuan["idPengajuanLembur"];
        $beforeOrAfter = $pengajuan["beforeOrAfter"];
        $path = $pengajuan["path"];

        $query = "INSERT INTO foto_pengajuan (id_pengajuan_lembur,before_or_after,path) values (?,?,?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param(
                "iss",
                $idPengajuanLembur,
                $beforeOrAfter,
                $path
            );
            $stmt->execute();
            $stmt->close();
            return true;
        }
    }
}

?>