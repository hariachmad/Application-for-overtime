<?php


require_once realpath(dirname(__FILE__) . "../../../repository/databaseService.php");
class ListPengajuanModel
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
        $search = isset($_GET['search']) ? $this->conn->real_escape_string($_GET['search']) : '';

        $query = "SELECT 
            pl.pengajuan_id, 
            k.username AS nama_karyawan, 
            pl.tanggal_lembur, 
            pl.jenis_proyek,
            pl.nama_proyek, 
            pl.jam_mulai, 
            pl.jam_selesai, 
            pl.durasi_lembur, 
            pl.alasan_lembur, 
            pl.daftar_pekerjaan, 
            pl.status_pengajuan,
            pl.foto_sebelum_path,
            pl.foto_sesudah_path,
            CASE 
                WHEN pl.status_pengajuan = 'disetujui' THEN a1.role
                WHEN pl.status_pengajuan = 'ditolak' THEN a2.role
                ELSE '-'
            END AS approver_role
        FROM pengajuan_lembur pl 
        LEFT JOIN karyawan k ON pl.karyawan_id = k.karyawan_id 
        LEFT JOIN admin a1 ON pl.disetujui_oleh = a1.admin_id
        LEFT JOIN admin a2 ON pl.ditolak_oleh = a2.admin_id";

        if (!empty($search)) {
            $query .= " WHERE k.username LIKE '%$search%'";
        }

        $query .= " ORDER BY pl.tanggal_pengajuan DESC";

        $result = $this->conn->query($query);

        // Cek apakah query berhasil
        if (!$result) {
            die("Query Error: " . $this->conn->error);
        }

        return $result;
    }
}


?>