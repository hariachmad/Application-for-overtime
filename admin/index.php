<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php';

error_log("Session data at the beginning: " . print_r($_SESSION, true));

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || !isset($_SESSION['admin_role'])) {
    error_log('Unauthorized access attempt: ' . print_r($_SESSION, true));
    header("Location: ../index.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$admin_role = $_SESSION['admin_role'];

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function logError($message) {
    error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, "error_log.txt");
}

// index.php

// Autoload controllers dan models
spl_autoload_register(function ($class_name) {
    include 'controllers/' . $class_name . '.php';
});

$url = $_GET['url'] ?? '/home';

// Routing sederhana
switch ($url) {
    case '/':
    case '/home':
        $controller = new PengajuanLemburController();
        $controller->index();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}

?>