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

    public function createUser($username,$password,$divisi){
        $query = "INSERT INTO karyawan (username, password, divisi) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sss',$username_, $password_, $divisi_);

        $username_= $username;
        $password_= $password;
        $divisi_= $divisi;
        
        return $stmt->execute();
    }
}

?>