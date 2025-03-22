<?php


require_once realpath(dirname(__FILE__) . "../../../repository/databaseService.php");
require_once(__DIR__ . "/../models/ListPengajuanModel.php");
class ListPengajuanModel
{
    private $conn;

    public function __construct()
    {
        // $this->conn = new DatabaseService()->getConn();
        $databaseService = new DatabaseService();
        $this->conn = $databaseService->getConn();
    }

    public function create($pengajuan)
    {
        $karyawan_id = $pengajuan["karyawan_id"];
        $tanggal_lembur = $pengajuan["tanggal_lembur"];
        $jenis_proyek = $pengajuan["jenis_proyek"];
        $nama_proyek = $pengajuan["nama_proyek"];
        $jam_mulai = $pengajuan["jam_mulai"];
        $jam_selesai = $pengajuan["jam_selesai"];
        $alasan_lembur = $pengajuan["alasan_lembur"];
        $daftar_pekerjaan = $pengajuan["daftar_pekerjaan"];
        $mulai_lembur = strtotime($pengajuan["jam_mulai"] . ":00");
        $selesai_lembur = strtotime($pengajuan["jam_selesai"] . ":00");

        if($jam_selesai<$jam_mulai){
            $jam_selesai = $jam_selesai + 86400;
        }

        $selisihLembur = $selesai_lembur - $mulai_lembur;
        $selisihDec = number_format($selisihLembur / 3600, 2);
        $durasiLembur = gmdate("H:i:s", $selisihLembur);

        $query = "INSERT INTO pengajuan_lembur (
            karyawan_id, 
            tanggal_lembur, 
            jenis_proyek,
            nama_proyek,
            jam_mulai,
            jam_selesai,
            durasi_lembur,
            alasan_lembur,
            daftar_pekerjaan,
            status_pengajuan,
            tanggal_pengajuan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("isssssdss",
                $karyawan_id,
                $tanggal_lembur,
                $jenis_proyek,
                $nama_proyek,
                $jam_mulai,
                $jam_selesai,
                $selisihDec,
                $alasan_lembur,
                $daftar_pekerjaan
            );

        }
        $stmt->execute();
        $stmt->close();
        return true;
    }
    public function getRows()
    {
        $search = isset($_GET['search']) ? $this->conn->real_escape_string($_GET['search']) : '';

        $queryPengajuanLembur = "SELECT 
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
            $queryPengajuanLembur .= " WHERE k.username LIKE '%$search%'";
        }

        $queryPengajuanLembur .= " ORDER BY pl.tanggal_pengajuan DESC";

        $resultPengajuanLembur = $this->conn->query($queryPengajuanLembur);

        if (!$resultPengajuanLembur) {
            die("Query Error: " . $this->conn->error);
        }

        $queryFotoPengajuanBefore = "select pengajuan_lembur.karyawan_id,pengajuan_lembur.pengajuan_id,pengajuan_lembur.tanggal_pengajuan,foto_pengajuan.before_or_after,foto_pengajuan.path
                                from pengajuan_lembur right join foto_pengajuan
                                on pengajuan_lembur.pengajuan_id = foto_pengajuan.id_pengajuan_lembur where before_or_after = 'before'";

        $resultFotoPengajuanBefore = $this->conn->query($queryFotoPengajuanBefore);

        if (!$resultFotoPengajuanBefore) {
            die("Query Error: " . $this->conn->error);
        }

        $queryFotoPengajuanAfter = "select pengajuan_lembur.karyawan_id,pengajuan_lembur.pengajuan_id,pengajuan_lembur.tanggal_pengajuan,foto_pengajuan.before_or_after,foto_pengajuan.path
                                from pengajuan_lembur right join foto_pengajuan
                                on pengajuan_lembur.pengajuan_id = foto_pengajuan.id_pengajuan_lembur where before_or_after = 'after'";

        $resultFotoPengajuanAfter = $this->conn->query($queryFotoPengajuanAfter);

        if (!$resultFotoPengajuanAfter) {
            die("Query Error: " . $this->conn->error);
        }

        $result = [
            "pengajuanLembur" => $resultPengajuanLembur,
            "fotoPengajuanBefore" => $resultFotoPengajuanBefore,
            "fotoPengajuanAfter" => $resultFotoPengajuanAfter
        ];


        return $result;
    }
}


?>