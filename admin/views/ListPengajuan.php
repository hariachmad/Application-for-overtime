<?php $search = '' ?>

<!DOCTYPE html>
<html lang="en">

<>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> -->
    <link href="../dist/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>List Pengajuan Lembur</title>
    <style>
        .content {
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
            background: rgba(0, 0, 0, 0.8);
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
    <?php

    function decimalToTime($decimal)
    {
        // Pisahkan jam dan menit
        $hours = floor($decimal); // Ambil bagian jam
        $minutes = round(($decimal - $hours) * 60); // Hitung menit dari bagian desimal
    
        // Format menjadi string "jam:menit"
        return sprintf("%02d:%02d", $hours, $minutes);
    }
    ?>
    </head>

    <body>
        <?php require(realpath(dirname(__FILE__) . "../../components/admin_navbar.php")) ?>
        <div class="content">
            <div class="flex flex-col ml-[18.5rem]">
                <div class="flex flex-row">
                    <form class="search-form flex flex-row rounded gap-1" action="/bluelake/admin/list-pengajuan"
                        method="GET">
                        <input
                            class="w-[240px] h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            type="text" id="search" name="search" placeholder="Cari nama karyawan..."
                            value="<?php echo htmlspecialchars($search); ?>">
                        <button id="search-button"
                            class="bg-green-500 h-10 text-white px-4 rounded flex justify-center align-middle text-center"
                            type="submit">Cari</button>
                    </form>
                    <form method="GET" action="/bluelake/admin/list-pengajuan" class="flex flex-row gap-1 ml-12">
                        <div class="mb-4 flex flex-row ">
                            <input type="text" id="rentang_tanggal" name="rentang_tanggal"
                                class="w-[240px] px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Pilih rentang tanggal">
                        </div>
                        <button type="submit"
                            class="bg-green-500 h-10 text-white px-4 rounded flex items-center hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit
                        </button>
                    </form>
                </div>
                <div class="flex flex-row">
                    <form class="search-form" id="download-form">
                        <input type="hidden" name="form_type" value="download">
                        <button id="download-button">Generate</button>
                    </form>

                    <form
                        class="bg-green-500 h-10 text-white px-4 rounded ml-4 flex justify-center align-middle text-center"
                        action="index.php" method="post">
                        <input type="hidden" name="form_type" value="download-generate">
                        <button id="download-button-generate" type="submit">Download</button>
                    </form>

                </div>
            </div>
            <table class="content-table">
                <thead>
                    <tr>
                        <th id="A">Nama Karyawan</th>
                        <th id="B">Jenis Proyek</th>
                        <th id="C">Nama Proyek</th>
                        <th id="D">Tanggal Lembur</th>
                        <th id="E">Jam Lembur</th>
                        <th id="K">Durasi</th>
                        <th id="F">Daftar Pekerjaan</th>
                        <th id="G">Status</th>
                        <th id="H">Disetujui / Ditolak Oleh</th>
                        <th id="I">Bukti Foto Sebelum</th>
                        <th id="J">Bukti Foto Sesudah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fotoPengajuan = $result["fotoPengajuanBefore"]->fetch_all(MYSQLI_ASSOC);
                    if ($result["pengajuanLembur"]->num_rows > 0) {
                        while ($row = $result["pengajuanLembur"]->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nama_karyawan']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis_proyek']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_proyek']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_lembur'])); ?></td>
                                <td><?php echo htmlspecialchars($row['jam_mulai']) . ' - ' . htmlspecialchars($row['jam_selesai']); ?>
                                </td>
                                <td><?php echo decimalToTime(number_format($row['durasi_lembur'], 2)); ?></td>
                                <td>
                                    <?php
                                    $pekerjaan = json_decode($row['daftar_pekerjaan'], true);
                                    if (is_array($pekerjaan)) {
                                        echo "<ul>";
                                        foreach ($pekerjaan as $item) {
                                            echo "<li>" . htmlspecialchars($item) . "</li>";
                                        }
                                        echo "</ul>";
                                    } else {
                                        echo htmlspecialchars($row['daftar_pekerjaan']);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="status status-<?php echo strtolower($row['status_pengajuan']); ?>">
                                        <?php echo htmlspecialchars($row['status_pengajuan']); ?>
                                    </span>
                                </td>
                                <td><?php echo isset($row['approver_role']) ? htmlspecialchars($row['approver_role']) : '-'; ?>
                                </td>
                                <td>
                                    <?php
                                    foreach ($fotoPengajuan as $foto) {
                                        if ($foto["pengajuan_id"] == $row["pengajuan_id"]) {
                                            echo "<img class='' src='../user/" . $foto["path"] . "'>";
                                        }
                                    }

                                    // while ($rowFoto = $result["fotoPengajuanBefore"]->fetch_assoc()):
                                    //     echo "". htmlspecialchars("Test");
                                    //     if ($rowFoto["pengajuan_id"] == $row["pengajuan_id"]) {
                                    //         echo "<img class='' src='../user/" . $rowFoto["path"] . "'>";
                                    //     }
                                    // endwhile;
                            
                                    ?>
                                </td>
                                <td>
                                    <?php

                                    // while ($rowFoto = $result["fotoPengajuanAfter"]->fetch_assoc()) {
                                    //     if ($rowFoto["pengajuan_id"] == $row["pengajuan_id"]) {
                                    //         echo "<img class='' src='../user/" . $rowFoto["path"] . "'>";
                                    //     }
                                    // }
                            
                                    ?>
                                </td>
                            </tr>
                            <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='9'>Tidak ada data yang ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
        <script>
            flatpickr("#rentang_tanggal", {
                mode: "range",
                dateFormat: "d/m/Y",
                locale: "id", // Bahasa Indonesia
                allowInput: true,
            });
        </script>
        <script>
            const headerA = document.getElementById("A").innerText;
            const headerB = document.getElementById('B').innerText;
            const headerC = document.getElementById('C').innerText;
            const headerD = document.getElementById('D').innerText;
            const headerE = document.getElementById('E').innerText;
            const headerF = document.getElementById('F').innerText;
            const headerG = document.getElementById('G').innerText;
            const headerH = document.getElementById('H').innerText;
            const headerI = document.getElementById('I').innerText;
            const headerJ = document.getElementById('J').innerText;

            const searchInput = document.getElementById('search');
            const downloadButton = document.getElementById('download-button')
            const submitButton = document.getElementById('search-button');
            submitButton.disabled = true;

            searchInput.addEventListener("input", function () {
                if (searchInput.value.trim() === '') {
                    submitButton.disabled = true;
                } else {
                    submitButton.disabled = false;
                }
            });

            downloadButton.addEventListener("click", function () {
                const headers = [headerA, headerB, headerC, headerD, headerE, headerF, headerG, headerH, headerI, "", "", "", "", headerJ];
                const results = <?php echo json_encode($result) ?>;
                const formData = new FormData(document.getElementById('download-form'));
                formData.append("headers", headers);
                fetch('../index.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        alert('Generate File Berhasil');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });

            })


            // Fungsi untuk menyetujui lembur
            function approveOvertime(id) {
                if (confirm('Apakah Anda yakin ingin menyetujui pengajuan lembur ini?')) {
                    window.location.href = `./pages/list_pengajuan.php?id=${id}&action=approve`;
                }
            }

            // Fungsi untuk menolak lembur
            function rejectOvertime(id) {
                if (confirm('Apakah Anda yakin ingin menolak pengajuan lembur ini?')) {
                    window.location.href = `./pages/list_pengajuan.php?id=${id}&action=reject`;
                }
            }
        </script>
    </body>

</html>