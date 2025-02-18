<?php

require_once 'models/PengajuanLemburModel.php';
require_once '../repository/databaseService.php';

class PengajuanLemburController
{
    private $conn;

    public function __construct()
    {
        $db = new DatabaseService();
        $this->conn = $db->getConn();
    }

    public function index()
    {
        // $pengajuanLemburModel = new PengajuanLemburModel();
        // $pegajuanLembur = $userModel->getAllUsers();
        $pengajuanLemburModel = new PengajuanLemburModel();
        $result = $pengajuanLemburModel->getRows();

        include realpath(dirname(__FILE__)."../../views/PengajuanLembur.php");
    }

    public function handleForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ambil data dari form
            error_log('POST request received: ' . print_r($_POST, true));
            error_log('Session data: ' . print_r($_SESSION, true));

            header('Content-Type: application/json');

            try {
                $pengajuan_id = isset($_POST['pengajuan_id']) ? intval($_POST['pengajuan_id']) : 0;
                $action = isset($_POST['action']) ? $_POST['action'] : '';

                if (!$pengajuan_id || !$action) {
                    throw new Exception('Invalid request parameters');
                }

                if (!isset($_SESSION['user_id'])) {
                    throw new Exception('Admin session not found');
                }

                $admin_id = $_SESSION['user_id'];
                $admin_role = $_SESSION['admin_role'];
                if (!$admin_id) {
                    throw new Exception('Admin ID is null or empty');
                }
                $time = date('Y-m-d H:i:s');

                // Start transaction
                mysqli_begin_transaction($this->conn);

                try {
                    // Check if the request is still pending
                    $check_query = "SELECT status_pengajuan FROM pengajuan_lembur WHERE pengajuan_id = ? AND status_pengajuan = 'pending'";
                    $check_stmt = mysqli_prepare($this->conn, $check_query);
                    mysqli_stmt_bind_param($check_stmt, 'i', $pengajuan_id);
                    mysqli_stmt_execute($check_stmt);
                    $check_result = mysqli_stmt_get_result($check_stmt);

                    if (mysqli_num_rows($check_result) === 0) {
                        throw new Exception('Pengajuan sudah tidak dalam status pending');
                    }

                    if ($action === 'approve') {
                        error_log("Debug - Approving request: pengajuan_id=$pengajuan_id, admin_id=$admin_id");

                        $query = "UPDATE pengajuan_lembur 
                                  SET status_pengajuan = 'disetujui',
                                      disetujui_oleh = ?,
                                      tanggal_persetujuan = NOW(),
                                      approval_status = ?
                                  WHERE pengajuan_id = ? 
                                  AND status_pengajuan = 'pending'";

                        $stmt = mysqli_prepare($this->conn, $query);
                        if (!$stmt) {
                            throw new Exception('Failed to prepare approval statement: ' . mysqli_error($this->conn));
                        }

                        $status = "Disetujui oleh " . $admin_role;

                        if (!mysqli_stmt_bind_param($stmt, 'isi', $admin_id, $status, $pengajuan_id)) {
                            throw new Exception('Failed to bind approval parameters: ' . mysqli_stmt_error($stmt));
                        }

                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception('Failed to execute approval: ' . mysqli_stmt_error($stmt));
                        }

                        if (mysqli_stmt_affected_rows($stmt) === 0) {
                            throw new Exception('Pengajuan tidak dapat disetujui - mungkin sudah diproses atau tidak ditemukan');
                        }

                        mysqli_stmt_close($stmt);
                    } elseif ($action === 'reject') {
                        $query = "UPDATE pengajuan_lembur 
                                 SET status_pengajuan = 'ditolak',
                                     ditolak_oleh = ?,
                                     tanggal_penolakan = NOW(),
                                     approval_status = ?
                                 WHERE pengajuan_id = ?
                                 AND status_pengajuan = 'pending'";
                        $stmt = mysqli_prepare($this->conn, $query);
                        if (!$stmt) {
                            throw new Exception('Failed to prepare rejection statement: ' . mysqli_error($this->conn));
                        }

                        $status = "Ditolak oleh " . $admin_role;

                        if (!mysqli_stmt_bind_param($stmt, 'isi', $admin_id, $status, $pengajuan_id)) {
                            throw new Exception('Failed to bind rejection parameters: ' . mysqli_stmt_error($stmt));
                        }

                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception('Failed to execute rejection: ' . mysqli_stmt_error($stmt));
                        }

                        if (mysqli_stmt_affected_rows($stmt) === 0) {
                            throw new Exception('Pengajuan tidak dapat ditolak - mungkin sudah diproses atau tidak ditemukan');
                        }

                        mysqli_stmt_close($stmt);
                    } else {
                        throw new Exception('Invalid action');
                    }

                    // Log the action
                    $log_query = "INSERT INTO log_aktivitas (user_id, user_type, aktivitas, detail) 
                     VALUES (?, ?, ?, ?)";
                    $log_stmt = mysqli_prepare($this->conn, $log_query);
                    if ($log_stmt) {
                        $user_type = 'admin';
                        $aktivitas = ($action === 'approve' ? "Persetujuan" : "Penolakan") . " pengajuan lembur";
                        $detail = "Pengajuan lembur ID " . $pengajuan_id . " " . ($action === 'approve' ? "disetujui" : "ditolak") . " oleh " . $admin_role;

                        if (!mysqli_stmt_bind_param($log_stmt, 'isss', $admin_id, $user_type, $aktivitas, $detail)) {
                            throw new Exception('Failed to bind log parameters: ' . mysqli_stmt_error($log_stmt));
                        }

                        if (!mysqli_stmt_execute($log_stmt)) {
                            throw new Exception('Failed to create log: ' . mysqli_stmt_error($log_stmt));
                        }

                        mysqli_stmt_close($log_stmt);
                    } else {
                        throw new Exception('Failed to prepare log statement: ' . mysqli_error($this->conn));
                    }

                    mysqli_commit($this->conn);
                    echo json_encode([
                        'status' => 'success',
                        'message' => ($action === 'approve' ? "Berhasil disetujui" : "Pengajuan berhasil ditolak") . " oleh $admin_role",
                        'reload' => true
                    ]);

                } catch (Exception $e) {
                    mysqli_rollback($this->conn);
                    throw $e;
                }

            } catch (Exception $e) {
                error_log('Error in admin_base.php: ' . $e->getMessage());
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
            exit();

        } else {
            // Jika bukan method POST, redirect ke form
            header('Location: ../views/PengajuanLembur.php');
            exit;
        }
    }
}
?>