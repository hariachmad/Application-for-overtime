<?php

class LoginController
{

    public function index($username,$password)
    {
        $conn = new DatabaseService();
        $conn = $conn->getConn();

        $username = Utils::sanitize_input($username);

        if (empty($username) || empty($password)) {
            $error_message = "Username dan password harus diisi!";
        } else {
            error_log("Login attempt for username: " . $username);

            $admin = Utils::check_credentials($conn, 'admin', $username, $password);
            if ($admin) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $admin['admin_id'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];

                header('Location: admin/index.php');
                exit();
            }

            $karyawan = Utils::check_credentials($conn, 'karyawan', $username, $password);
            if ($karyawan) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $karyawan['karyawan_id'];
                $_SESSION['user_type'] = 'karyawan';
                $_SESSION['username'] = $karyawan['username'];

                header("Location: user/index.php");
                exit();
            }

            $error_message = "Username atau password salah!";
            error_log("Authentication failed for username: " . $username);
            $conn->close();
        }
    }
}



?>