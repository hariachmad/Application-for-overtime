<?php

class DatabaseService
{
private $servername = "localhost";
private $username = "root"; // Change this to your database username
private $password = ""; // Change this to your database password
private $dbname = "bluelake";

// Create conneection
private $db;

public function getConn(){
    $this->db=new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    $this->db->set_charset("utf8mb4");
    return $this->db;
}

// Check connection
public function checkConnection(){
    if (!$this->getConn()){
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($this->getConn()->connect_error) {
        die("Connection failed: " . $this->getConn()->connect_error);
    }

    echo "<script>console.log(" . json_encode("Connection Success") . ");</script>";
}
}
?>