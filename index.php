<?php
require_once(__DIR__ . "/repository/databaseService.php");
require_once(__DIR__ ."/admin/controllers/PengajuanLemburController.php");
require_once(__DIR__ ."/admin/controllers/ListPengajuanController.php");
require_once(__DIR__ ."/utils/Utils.php");
require_once(__DIR__ ."/admin/controllers/LoginController.php");
require_once(__DIR__ ."/admin/controllers/DataKaryawanController.php");
require_once(__DIR__ ."/admin/controllers/TambahKaryawanController.php");

$error_message = "";
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$pattern = "#^/bluelake/(.*)$#";
preg_match($pattern, parse_url($requestUri, PHP_URL_PATH), $matches);

if ($requestMethod == "POST") {
    $form_type = $_POST['form_type'];

    switch ($form_type) {
        case 'login':
            $controller = new LoginController();
            $controller->index($_POST['username'],$_POST['password']);
            break;
        case 'register':
            $controller = new TambahKaryawanController();
            $result = $controller->createKaryawan($_POST['username'],$_POST['password'],$_POST['divisi']);
            if ($result) {
                $controller->index();
            }
            break;
        default:
            echo htmlspecialchars('400 POST NOT FOUND');
            break;
    }
    exit();
}

switch ($matches[1]) {
    case "":
    case "index.php":
    case "HOME":
        require(__DIR__ . "/login.php");
        break;
    case "admin/tambah-karyawan":
        $controller = new TambahKaryawanController();
        $controller->index();
        break;
    case "admin/index.php":
        $controller = new PengajuanLemburController();
        $controller->index();
        break;
    case "admin/list-pengajuan":
        $controller = new ListPengajuanController();
        $controller->index();
        break;
    case "admin/data-karyawan":
        $controller = new DataKaryawanController();
        $controller->index();
        break;
    default:
        var_dump($matches);
        echo htmlspecialchars('404 NOT FOUND');
}
?>