<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP dengan Tailwind CSS</title>
    <link href="../tailwind.css" rel="stylesheet">
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
        <div
            style="margin-right: 120px;padding-inline: 10px; margin-top: 40px; width: 100%; display: flex; justify-content: center;">
            <table style="width: 80%;">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-center text-sm font-medium text-white">ID</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-white">Nama</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-white">Divisi</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-white"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) { ?>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo $row['karyawan_id']; ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700 w-[250px] text-center"><span
                                        class="text"><?php echo $row['username']; ?></span><input type="name"
                                        style="display: none;" class="editable text-center"
                                        value="<?php echo $row['username']; ?>"></td>
                                <td class="px-4 py-2 text-sm text-gray-700 w-[300px] text-center"><span
                                        class="text"><?php echo $row['divisi']; ?></span><input type="divisi"
                                        style="display: none;" class="editable text-center"
                                        value="<?php echo $row['divisi']; ?>"></td>
                                <td class="flex justify-center"><button onCLick="editRow(this)"
                                        class=" bg-blue-400 px-4 py-1 text-white font-semibold rounded-lg shadow-lg transform transition-all hover:bg-blue-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">Edit</button>
                                        <button data-id="<?php echo $row['karyawan_id'] ?>"
                                        onCLick="saveRow(this)" style="display: none;"
                                        class="save-btn bg-green-400 px-4 py-1 text-white font-semibold rounded-lg shadow-lg transform transition-all hover:bg-green-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">Save</button>
                                    <button data-id="<?php echo $row['karyawan_id'] ?>"
                                        class="deleteBtn ml-4 bg-red-500 px-4 py-1 text-white font-semibold rounded-lg shadow-lg transform transition-all hover:bg-red-700 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
    <script>
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const id = this.getAttribute('data-id');
                const nama = row.querySelector('input[type="name"]').value;
                const divisi = row.querySelector('input[type="divisi"]').value;

                fetch('../index.php', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    // body: JSON.stringify({ id: id, name: name, email: email })
                    body: new URLSearchParams({
                        form_type: "update",
                        id : id,
                        nama : nama,
                        divisi : divisi
                    }).toString()
                })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
            });
        });

        const deleteButtons = document.querySelectorAll('.deleteBtn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                console.log(id);
                fetch("../index.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                        id: id,
                        form_type: "delete"
                    }).toString()
                })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                    })
                const row = this.closest('tr');
                row.remove();
            });
        });

        function editRow(button) {
            const row = button.closest('tr');

            const textFields = row.querySelectorAll('.text');
            const inputFields = row.querySelectorAll('.editable');
            textFields.forEach(field => field.style.display = 'none');
            inputFields.forEach(field => field.style.display = 'inline');

            button.style.display = 'none';
            row.querySelector('.save-btn').style.display = 'inline';
        }

        function saveRow(button) {
            const row = button.closest('tr');

            const textFields = row.querySelectorAll('.text');
            const inputFields = row.querySelectorAll('.editable');

            inputFields.forEach((field, index) => {
                textFields[index].textContent = field.value;
                textFields[index].style.display = 'inline';
                field.style.display = 'none';
            });

            button.style.display = 'none';
            row.querySelector('button').style.display = 'inline';
        }
    </script>



</body>

</html>