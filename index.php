<?php

require_once(__DIR__ . "/repository/databaseService.php");
require_once(__DIR__ ."/admin/controllers/PengajuanLemburController.php");
require_once(__DIR__ ."/admin/controllers/ListPengajuanController.php");
require_once(__DIR__ ."/utils/Utils.php");

$error_message = "";
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$pattern = "#^/bluelake/(.*)$#";
preg_match($pattern, parse_url($requestUri, PHP_URL_PATH), $matches);

if ($requestMethod == "POST") {
    $username = Utils::sanitize_input($_POST['username']);
    $password = $_POST['password'];

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

switch ($matches[1]) {
    case "":
    case "HOME":
        require(__DIR__ . "/login.php");
        break;
    case "admin/index.php":
        $controller = new PengajuanLemburController();
        $controller->index();
        break;
    case "admin/list-pengajuan":
        $controller = new ListPengajuanController();
        $controller->index();
        break;
    default:
        echo htmlspecialchars('404 NOT FOUND');
}
?>