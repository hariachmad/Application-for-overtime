<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }

        table {
            width: 100%;
            margin-left: 20%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-left: 20%;
            color: #1e2538;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #1e2538;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 8px;
            font-weight: 500;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .reject-btn {
            background-color: #dc3545;
            color: white;
        }

        .reject-btn:hover {
            background-color: #c82333;
        }

        .status-pending {
            color: #f59e0b;
            font-weight: 500;
        }

        .photo-evidence {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <?php require(realpath(dirname(__FILE__) . "../../components/admin_navbar.php")) ?>

    <h1>Daftar Pengajuan Lembur</h1>
    <table>
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Tanggal Lembur</th>
                <th>Durasi</th>
                <th>Alasan</th>
                <th>Foto Sebelum</th>
                <th>Foto Sesudah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['karyawan_nama']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_lembur'])); ?></td>
                        <td><?php echo number_format($row['durasi_lembur'], 1); ?></td>
                        <td><?php echo htmlspecialchars($row['alasan_lembur']); ?></td>
                        <td>
                            <?php if (!empty($row['foto_sebelum_path'])): ?>
                                <img src="<?php echo htmlspecialchars($row['foto_sebelum_path']); ?>" alt="Foto Sebelum"
                                    class="photo-evidence" style="max-width: 100px; height: auto;">
                            <?php else: ?>
                                Tidak ada foto
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['foto_sesudah_path'])): ?>
                                <img src="<?php echo htmlspecialchars($row['foto_sesudah_path']); ?>" alt="Foto Sesudah"
                                    class="photo-evidence" style="max-width: 100px; height: auto;">
                            <?php else: ?>
                                Tidak ada foto
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status_pengajuan'] === 'pending'): ?>
                                <span style="color: #f59e0b;">Menunggu Persetujuan</span>
                            <?php else: ?>
                                <?php
                                echo $row['status_pengajuan'] === 'disetujui' ?
                                    '<span style="color: #28a745;">Disetujui</span>' :
                                    '<span style="color: #dc3545;">Ditolak</span>';
                                ?>
                                <?php if ($row['approver_role']): ?>
                                    <br>oleh <?php echo htmlspecialchars($row['approver_role']); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['status_pengajuan'] === 'pending'): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="form_type" value="approve">
                                    <input type="hidden" name="pengajuan_id" value="<?php echo $row['pengajuan_id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="action-btn approve-btn"
                                        style="background-color: #28a745; color: white; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer; margin-right: 4px;">
                                        Approve
                                    </button>
                                </form>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="form_type" value="reject">
                                    <input type="hidden" name="pengajuan_id" value="<?php echo $row['pengajuan_id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="action-btn reject-btn"
                                        style="background-color: #dc3545; color: white; border: none; border-radius: 4px; padding: 8px 16px; cursor: pointer;">
                                        Reject
                                    </button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Tidak ada pengajuan lembur yang pending.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.onsubmit = function (e) {
                    e.preventDefault();
                    const action = this.querySelector('input[name="action"]').value;
                    const confirmMsg = action === 'approve' ?
                        'Apakah Anda yakin ingin menyetujui pengajuan ini?' :
                        'Apakah Anda yakin ingin menolak pengajuan ini?';


                    if (confirm(confirmMsg)) {
                        const formData = new FormData(this);

                        fetch('../index.php', {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin'
                        })
                            .then(response => {
                                return response.text()
                            })
                            .then(data => {
                                console.log(data);
                                alert('Data berhasil dikirim!');
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan. Silakan coba lagi.');
                            });
                    } else {
                        const formData = new FormData(this);

                        fetch('../index.php', {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin'
                        })
                            .then(response => response.text())
                            .then(.then(data => {
                                console.log(data);
                                alert('Data berhasil dikirim!');
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan. Silakan coba lagi.');
                            });)
                    }
                };
            });
        });
    </script>
</body>

</html>