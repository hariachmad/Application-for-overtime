<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blue_lake";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Validasi koneksi database
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Inisialisasi variabel pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query dasar
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

// Rest of the query conditions remain the same
if (!empty($search)) {
    $query .= " WHERE k.username LIKE '%$search%'";
}

$query .= " ORDER BY pl.tanggal_pengajuan DESC";

$result = $conn->query($query);

// Cek apakah query berhasil
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pengajuan Lembur</title>
    <style>
        .content  {
            width: 90%;
        }

        .content-table {
            width: 80%;
            margin-left: 25%;
            margin-right: 5%;
            border-collapse: collapse;
            margin-top: 5px;
            background: white;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .content-table th {
            border: 1px solid #ddd;
            background: #1e2538;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .content-table td {
            border: 1px solid #ddd;
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .content-table tr:hover {
            background: #f8f9fa;
        }

        .status {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            margin: 2px;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .thumbnail {
            max-width: 100px;
            height: auto;
            cursor: pointer;
        }

        /* Modal untuk preview gambar */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
        }

        .modal-content img {
            max-width: 100%;
            max-height: 90vh;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .search-form {
            margin-left: 25%;
            margin-bottom: 10px;
            text-align: left;
        }
        .search-form input[type="text"] {
            padding: 8px;
            width: 250px;
        }
        .search-form button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php include '../components/admin_navbar.php'; ?>
    <div class="content">
        <form class="search-form" action="" method="GET">
            <input type="text" name="search" placeholder="Cari nama karyawan..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Cari</button>
        </form>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Jenis Proyek</th>
                    <th>Nama Proyek</th>
                    <th>Tanggal Lembur</th>
                    <th>Jam Lembur</th>
                    <th>Daftar Pekerjaan</th>
                    <th>Bukti Kerja</th>
                    <th>Status</th>
                    <th>Disetujui / Ditolak Oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_karyawan']); ?></td>
                        <td><?php echo htmlspecialchars($row['jenis_proyek']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_proyek']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_lembur'])); ?></td>
                        <td><?php echo htmlspecialchars($row['jam_mulai']) . ' - ' . htmlspecialchars($row['jam_selesai']); ?></td>
                        <td>
                            <?php 
                            $pekerjaan = json_decode($row['daftar_pekerjaan'], true);
                            if(is_array($pekerjaan)) {
                                echo "<ul>";
                                foreach($pekerjaan as $item) {
                                    echo "<li>" . htmlspecialchars($item) . "</li>";
                                }
                                echo "</ul>";
                            } else {
                                echo htmlspecialchars($row['daftar_pekerjaan']);
                            }
                            ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <div>
                                    <strong>Sebelum:</strong><br>
                                    <?php if (!empty($row['foto_sebelum_path'])): ?>
                                        <?php
                                        $path = $row['foto_sebelum_path'];
                                        $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
                                        if (file_exists($full_path)) {
                                            echo "<img src='/" . htmlspecialchars($path) . "' 
                                                class='thumbnail'
                                                onclick='showImage(this.src)'
                                                alt='Foto Sebelum'
                                                style='max-width: 100px; height: auto;'>";
                                        } else {
                                            echo "<span class='no-proof'>File tidak ditemukan di: " . htmlspecialchars($path) . "</span>";
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span class="no-proof">Path foto kosong</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong>Sesudah:</strong><br>
                                    <?php if (!empty($row['foto_sesudah_path'])): ?>
                                        <?php
                                        $path = $row['foto_sesudah_path'];
                                        $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
                                        if (file_exists($full_path)) {
                                            echo "<img src='/" . htmlspecialchars($path) . "' 
                                                class='thumbnail'
                                                onclick='showImage(this.src)'
                                                alt='Foto Sesudah'
                                                style='max-width: 100px; height: auto;'>";
                                        } else {
                                            echo "<span class='no-proof'>File tidak ditemukan di: " . htmlspecialchars($path) . "</span>";
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span class="no-proof">Path foto kosong</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="status status-<?php echo strtolower($row['status_pengajuan']); ?>">
                                <?php echo htmlspecialchars($row['status_pengajuan']); ?>
                            </span>
                        </td>
                        <td><?php echo isset($row['approver_role']) ? htmlspecialchars($row['approver_role']) : '-'; ?></td>
                    </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='9'>Tidak ada data yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>


        <!-- Modal untuk preview gambar -->
        <div id="imageModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="modal-content">
                <img id="modalImage" src="/placeholder.svg" alt="Preview">
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk preview gambar
        function showImage(src) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = "block";
            modalImg.src = src;
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = "none";
        }

        // Fungsi untuk menyetujui lembur
        function approveOvertime(id) {
            if(confirm('Apakah Anda yakin ingin menyetujui pengajuan lembur ini?')) {
                window.location.href = `./pages/list_pengajuan.php?id=${id}&action=approve`;
            }
        }

        // Fungsi untuk menolak lembur
        function rejectOvertime(id) {
            if(confirm('Apakah Anda yakin ingin menolak pengajuan lembur ini?')) {
                window.location.href = `./pages/list_pengajuan.php?id=${id}&action=reject`;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>