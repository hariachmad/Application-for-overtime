<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP dengan Tailwind CSS</title>
    <link href="../output.css" rel="stylesheet">
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
    </style>
</head>

<body style="display: flex; justify-content: center;" class="bg-gray-300 ">
    <?php require(realpath(dirname(__FILE__) . "../../components/admin_navbar.php")) ?>
    <div
        style="height: 100vh; margin-left: 300px;display :flex; flex-direction : column; align-items: center; width: 80vw;">
        <div style=" font-family: 'Courier New', Courier, monospace; margin-top: 10px;" class="title text-3xl">
            <h4>Data Karyawan</h4>
        </div>
        <div style="margin-right: 120px;padding-inline: 10px; margin-top: 40px; width: 100%; display: flex; justify-content: center;">
            <table style="width: 80%;">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">ID</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Divisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) { ?>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo $row['karyawan_id']; ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo $row['username']; ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo $row['divisi']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>