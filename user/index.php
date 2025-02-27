<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and is a karyawan
// if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'karyawan') {
//     header("Location: index.php");
//     exit();
// }

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blue_lake";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$karyawan_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal_lembur = $conn->real_escape_string($_POST['tanggal_lembur']);
    $jenis_proyek = $conn->real_escape_string($_POST['jenis_proyek']);
    $nama_proyek = $conn->real_escape_string($_POST['nama_proyek']); // Add this line
    $jam_mulai = $conn->real_escape_string($_POST['jam_mulai']);
    $jam_selesai = $conn->real_escape_string($_POST['jam_selesai']);
    $alasan_lembur = $conn->real_escape_string($_POST['alasan_lembur']);
    $daftar_pekerjaan = $conn->real_escape_string($_POST['daftar_pekerjaan']);

    // Hitung durasi lembur
    $durasi_result = $conn->query("SELECT TIMEDIFF('$jam_selesai', '$jam_mulai') AS durasi");
    $durasi_row = $durasi_result->fetch_assoc();
    $durasi_lembur = $durasi_row['durasi'];

    // Convert durasi to hours for storage
    $time_parts = explode(':', $durasi_lembur);
    $hours = $time_parts[0] + ($time_parts[1]/60) + ($time_parts[2]/3600);
    $durasi_jam = number_format($hours, 1);

    // Handle file uploads
    error_log("POST request received. Data: " . print_r($_POST, true));
    error_log("Files: " . print_r($_FILES, true));
    $upload_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $foto_sebelum_path = "";
    $foto_sesudah_path = "";
    $upload_error = false;

    // Handle foto sebelum
    if (isset($_FILES["foto_sebelum"]) && $_FILES["foto_sebelum"]["error"] == 0) {
        $foto_sebelum = $_FILES["foto_sebelum"];
        $foto_sebelum_name = time() . '_before_' . basename($foto_sebelum["name"]);
        $foto_sebelum_path = "uploads/" . $foto_sebelum_name;
        
        if (!move_uploaded_file($foto_sebelum["tmp_name"], $upload_dir . $foto_sebelum_name)) {
            $error_message = "Gagal mengupload foto sebelum lembur";
            $upload_error = true;
        }
    } else {
        error_log("Error uploading foto_sebelum: " . print_r($_FILES["foto_sebelum"], true));
        $error_message = "Foto sebelum lembur wajib diupload";
        $upload_error = true;
    }

    // Handle foto sesudah
    if (isset($_FILES["foto_sesudah"]) && $_FILES["foto_sesudah"]["error"] == 0) {
        $foto_sesudah = $_FILES["foto_sesudah"];
        $foto_sesudah_name = time() . '_after_' . basename($foto_sesudah["name"]);
        $foto_sesudah_path = "uploads/" . $foto_sesudah_name;
        
        if (!move_uploaded_file($foto_sesudah["tmp_name"], $upload_dir . $foto_sesudah_name)) {
            $error_message = "Gagal mengupload foto sesudah lembur";
            $upload_error = true;
        }
    } else {
        error_log("Error uploading foto_sesudah: " . print_r($_FILES["foto_sesudah"], true));
        $error_message = "Foto sesudah lembur wajib diupload";
        $upload_error = true;
    }

    // Only proceed with database insertion if file uploads are successful
    if (!$upload_error) {
        $stmt = $conn->prepare("INSERT INTO pengajuan_lembur (
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
            tanggal_pengajuan,
            foto_sebelum_path,
            foto_sesudah_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?, ?)");

        if ($stmt) {
            $stmt->bind_param("isssssdssss",
                $karyawan_id,
                $tanggal_lembur,
                $jenis_proyek,
                $nama_proyek,
                $jam_mulai,
                $jam_selesai,
                $durasi_jam,
                $alasan_lembur,
                $daftar_pekerjaan,
                $foto_sebelum_path,
                $foto_sesudah_path
            );

            if ($stmt->execute()) {
                $success_message = "Pengajuan lembur berhasil disubmit!";
                error_log("Overtime request submitted successfully for user ID: " . $karyawan_id);
            } else {
                $error_message = "Error: " . $stmt->error;
                error_log("Database insertion error: " . $stmt->error);
                // Clean up uploaded files if database insertion fails
                if (file_exists($upload_dir . $foto_sebelum_name)) {
                    unlink($upload_dir . $foto_sebelum_name);
                }
                if (file_exists($upload_dir . $foto_sesudah_name)) {
                    unlink($upload_dir . $foto_sesudah_name);
                }
            }
            $stmt->close();
        } else {
            $error_message = "Error in preparing statement: " . $conn->error;
            error_log("Statement preparation error: " . $conn->error);
        }
    }
}
$query = "
    SELECT 
        pl.*,
        CASE 
            WHEN pl.status_pengajuan = 'ditolak' THEN 
                CONCAT('Ditolak oleh ', pl.approval_status)
            WHEN pl.status_pengajuan = 'disetujui' THEN 
                CONCAT('Disetujui oleh ', pl.approval_status)
            ELSE 'Menunggu Persetujuan'
        END as status_detail
    FROM pengajuan_lembur pl
    WHERE pl.karyawan_id = ?
    ORDER BY pl.tanggal_pengajuan DESC";

$stmt = $conn->prepare($query); 
if ($stmt) {
    $stmt->bind_param("i", $karyawan_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Error preparing statement: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlueLake - Employee Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #1a1f2e;
            color: #1f2937;
        }

        .logo-container {
            display: flex;
            justify-content: center; /* Memusatkan secara horizontal */
            align-items: center; /* Memusatkan secara vertikal */
            padding-bottom: 10px;
        }

        .logo-image {
            height: 100px; /* Ukuran logo */
            width: auto; /* Menjaga proporsi logo */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1, h2 {
            margin-bottom: 1rem;
        }

        .form-container, .requests-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input[type="date"],
        input[type="number"],
        textarea,
        input[type="file"],
        input[type="time"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 1rem;
            background-color: white;
        }

        textarea {
            resize: vertical;
        }

        textarea .nama_proyek {
            height: 5px;
            max-height: 100px; /* Batasi tinggi maksimum jika perlu */
            resize: none;
        }

        button {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #2563eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .success-message {
            background-color: #10b981;
            color: #ffffff;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .error-message {
            background-color: #ef4444;
            color: #ffffff;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .disetujui-status {
            color: #10b981;
            font-weight: 500;
        }
        .ditolak-status {
            color: #ef4444;
            font-weight: 500;
        }
        .menunggu-persetujuan-status {
            color: #f59e0b;
            font-weight: 500;
        }
        .status-detail {
            font-size: 0.9em;
            color: #666;
            margin-top: 4px;
        }
        select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 1rem;
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="../public/image/bluelake.png" alt="../public/image/bluelake.png" class="logo-image">
        </div>

        <div class="form-container">
            <?php
            if (isset($success_message)) {
                echo "<div class='success-message'>$success_message</div>";
            }
            if (isset($error_message)) {
                echo "<div class='error-message'>$error_message</div>";
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="tanggal_lembur">Tanggal Lembur:</label>
                    <input type="date" id="tanggal_lembur" name="tanggal_lembur" required>
                </div>
                <div class="form-group">
                    <label for="jenis_proyek">Jenis Proyek:</label>
                    <select id="jenis_proyek" name="jenis_proyek" required>
                        <option value="">Pilih Jenis Proyek</option>
                        <option value="sipil">Sipil</option>
                        <option value="furniture">Furniture</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nama_proyek">Nama Proyek:</label>
                    <textarea id ="nama_proyek" name="nama_proyek" required></textarea>
                </div>
                <div class="form-group">
                    <label for="jam_mulai">Jam Mulai Lembur:</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" required>
                </div>
                <div class="form-group">
                    <label for="jam_selesai">Jam Selesai Lembur:</label>
                    <input type="time" id="jam_selesai" name="jam_selesai" required>
                </div>
                <div class="form-group">
                    <label for="alasan_lembur">Alasan Pengajuan Lembur:</label>
                    <textarea id="alasan_lembur" name="alasan_lembur" required></textarea>
                </div>
                <div class="form-group">
                    <label for="daftar_pekerjaan">Daftar pekerjaan yang dilakukan saat lembur:</label>
                    <textarea id="daftar_pekerjaan" name="daftar_pekerjaan" required></textarea>
                </div>
                <div class="form-group">
                    <label for="foto_sebelum">Foto Sebelum Lembur:</label>
                    <input type="file" id="foto_sebelum" name="foto_sebelum" accept="image/*" required>
                    <small class="form-text text-muted">Format yang diizinkan: JPG, PNG, GIF. Maksimal 5MB.</small>
                </div>
                <div class="form-group">
                    <label for="foto_sesudah">Foto Sesudah Lembur:</label>
                    <input type="file" id="foto_sesudah" name="foto_sesudah" accept="image/*" required>
                    <small class="form-text text-muted">Format yang diizinkan: JPG, PNG, GIF. Maksimal 5MB.</small>
                </div>

                <button type="submit">Submit Request</button>

                <script>
                // Add client-side file validation
                document.querySelector('form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Form submitted');
                    var formData = new FormData(this);
                    for (var pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                    this.submit();
                });
                document.querySelectorAll('input[type="file"]').forEach(function(input) {
                    input.addEventListener('change', function() {
                        const file = this.files[0];
                        const maxSize = 5 * 1024 * 1024; // 5MB
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        
                        if (file) {
                            if (file.size > maxSize) {
                                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                                this.value = '';
                            } else if (!allowedTypes.includes(file.type)) {
                                alert('Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.');
                                this.value = '';
                            }
                        }
                    });
                });
                </script>
            </form>
        </div>

        <div class="requests-container">
            <h2>Riwayat Pengajuan Lembur Anda</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Lembur</th>
                        <th>Durasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($row["tanggal_pengajuan"])) . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($row["tanggal_lembur"])) . "</td>";
                            echo "<td>" . $row["durasi_lembur"] . " jam</td>";
                            echo "<td>";
                            echo ucfirst($row["status_pengajuan"]);
                            echo "<div class='status-detail'>" . htmlspecialchars($row["approval_status"] ?? '') . "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Belum ada pengajuan lembur.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>