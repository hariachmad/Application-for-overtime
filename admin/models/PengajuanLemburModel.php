<?php


require_once realpath(dirname(__FILE__) . "../../../repository/databaseService.php");
class PengajuanLemburModel
{
    private $conn;

    public function __construct()
    {
        // $this->conn = new DatabaseService()->getConn();
        $databaseService = new DatabaseService();
        $this->conn = $databaseService->getConn();
    }

    public function reject($id, $admin_id, $admin_role)
    {
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

        if (!mysqli_stmt_bind_param($stmt, 'isi', $admin_id, $status, $id)) {
            throw new Exception('Failed to bind rejection parameters: ' . mysqli_stmt_error($stmt));
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to execute rejection: ' . mysqli_stmt_error($stmt));
        }

        if (mysqli_stmt_affected_rows($stmt) === 0) {
            throw new Exception('Pengajuan tidak dapat ditolak - mungkin sudah diproses atau tidak ditemukan');
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    public function approve($id, $admin_id, $admin_role): bool
    {
        $status = "Disetujui oleh " . $admin_role;
            $query = "UPDATE pengajuan_lembur 
                                    SET status_pengajuan = 'disetujui',
                                        disetujui_oleh = '$admin_id',
                                        tanggal_persetujuan = NOW(),
                                        approval_status = '$status'  WHERE pengajuan_id = '$id' AND status_pengajuan = 'pending'";
        if (mysqli_query($this->conn, $query)) {
            echo "Data berhasil dimasukkan!";
            mysqli_close($this->conn);
            return true;
        } else {
            echo "Error: " . mysqli_error($this->conn);
            mysqli_close($this->conn);
            return false;
        }  
        // $stmt = $this->conn->prepare($query);
        // $stmt->bind_param("iss", $admin_id_,$status_,$id_);

        // $admin_id_=$admin_id;
        // $status_=$status;
        // $id_=$id;

        // if ($stmt->execute()) {
        //     $stmt->close();
        //     $this->conn->close();
        //     return True;
        // } else {
        //     $stmt->close();
        //     $this->conn->close();
        //     return False;
        // }
    }

    public function getRows()
    {

        $query = "
    SELECT 
        pl.*,
        k.username AS karyawan_nama,
        CASE 
            WHEN pl.status_pengajuan = 'disetujui' THEN a1.role
            WHEN pl.status_pengajuan = 'ditolak' THEN a2.role
            ELSE NULL
        END AS approver_role,
        pl.pengajuan_id as nomor
    FROM pengajuan_lembur pl
    JOIN karyawan k ON pl.karyawan_id = k.karyawan_id 
    LEFT JOIN admin a1 ON pl.disetujui_oleh = a1.admin_id
    LEFT JOIN admin a2 ON pl.ditolak_oleh = a2.admin_id
    ORDER BY pl.tanggal_pengajuan DESC";

        if (!empty($search)) {
            $query .= " WHERE k.username LIKE '%$search%'";
        }

        $result = $this->conn->query($query);

        if (!$result) {
            die("Error fetching requests: " . $this->conn->error);
        }

        return $result;
    }
}

?>