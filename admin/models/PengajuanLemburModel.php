<?php


require_once realpath(dirname(__FILE__)."../../../repository/databaseService.php");
class PengajuanLemburModel
{
    private $conn;

    public function __construct()
    {
        // $this->conn = new DatabaseService()->getConn();
        $databaseService = new DatabaseService();
        $this->conn = $databaseService->getConn();
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