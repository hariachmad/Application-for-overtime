<?php

class Utils
{
    public static function sanitize_input($input)
    {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    public static function check_credentials($conn, $table, $username, $password)
{
    $conn = new DatabaseService();
    $conn = $conn->getConn();
    $sql = "SELECT * FROM $table WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return false;
}
}
?>