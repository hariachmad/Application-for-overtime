<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="/vite.svg" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="../output.css" rel="stylesheet">
  <title>Vite App</title>
</head>

<body>
  <?php require(realpath(dirname(__FILE__) . "../../components/admin_navbar.php")) ?>
  <div class="min-h-screen pt-10" style="background-image: linear-gradient(115deg, #1E2538, #d8dadd)">
    <div style="margin-left: 100px; margin-top: -30px;" class="container">
      <div class="flex flex-col lg:flex-row w-10/12 lg:w-8/12 bg-white rounded-xl mx-auto shadow-lg overflow-hidden">
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center p-12 bg-no-repeat bg-cover bg-center"
          style="background-image: url('../public/image/Register-Background.png');">
          <img src="../public/image/bluelake.png" alt="logo" class="w-30 h-20 mb-5">
        </div>
        <div class="w-full lg:w-1/2 py-16 px-12">
          <h2 class="text-3xl mb-4">Registrasi Karyawan</h2>
          <p class="mb-4">
            Silahkan isi form di bawah ini
          </p>
          <form id="myForm">
            <input type="hidden" name="form_type" value="register">
            <div class="grid grid-cols-2 gap-5">
              <input onchange="updateInputValue()" id="firstName" type="text" placeholder="Nama Depan"
                class="border border-gray-400 py-1 px-2">
              <input onchange="updateInputValue()" id="lastName" type="text" placeholder="Nama Belakang"
                class="border border-gray-400 py-1 px-2">
            </div>
            <div class="mt-5">
              <input id="userName" name="username" type="text" placeholder="username" disabled
                class="border border-gray-400 py-1 px-2 w-full">
            </div>
            <div class="mt-5">
              <input onkeydown="updateInputValue()" type="password" id="password" placeholder="Password"
                class="border border-gray-400 py-1 px-2 w-full">
            </div>
            <div class="mt-5">
              <input disabled name="password" id="confirmPassword" type="password" placeholder="Confirm Password"
                class="border border-gray-400 py-1 px-2 w-full">
            </div>
            <div class="mt-4">
              <label for="dropdown" class="block text-sm font-medium text-gray-700">Divisi</label>
              <select id="dropdown" name="divisi"
                class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="IT">IT</option>
                <option value="Finance">Finance</option>
                <option value="Inventory Control">Inventory Control</option>
              </select>
            </div>
            <div id="error-message" class="mt-5" style="color: red;">Password Don't Match / Empty</div>
            <div class="mt-5">
              <button onclick="handleSubmit()" disabled id="submit"
                class="w-full bg-[#1E2538] py-3 text-center text-white">Registrasi</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>

    const inputFirstName = document.getElementById('firstName');
    const inputLastName = document.getElementById('lastName');
    const inputUsername = document.getElementById('userName');
    const errorMessage = document.getElementById('error-message');
    const confirmPassword = document.getElementById("confirmPassword");
    const inputPassword = document.getElementById('password');
    let divisi = 'IT';
    let username = "";
    let password = ""

    const buttonSubmit = document.getElementById('submit');
    inputPassword.addEventListener("input", function () {
      const inputValue = this.value;

      if (inputValue != "") {
        document.getElementById('confirmPassword').disabled = false;
      } else {
        document.getElementById('confirmPassword').disabled = true;
      }
    });

    confirmPassword.addEventListener("input", function () {
      console.log(confirmPassword.value)
      if (inputPassword.value !== confirmPassword.value) {
        errorMessage.style.display = "block";
        buttonSubmit.disabled = true;
      } else {
        errorMessage.style.display = "none";
        buttonSubmit.disabled = false;
        password = confirmPassword.value;
      }
    });

    const dropdown = document.getElementById('dropdown');
    dropdown.addEventListener("change", function () {
      divisi = dropdown.value;
      console.log("username : ", username);
      console.log("password : ", password);
      console.log("divisi: ", divisi);
    })


    function updateInputValue() {
      inputUsername.value = inputFirstName.value + inputLastName.value;
      username = inputUsername.value;
    }

    function handleSubmit() {
      const additionalData = {
        username: username,
        password: password,
        divisi: divisi
      }
      const formData = new FormData(document.getElementById('myForm'));
      for (const key in additionalData) {
        formData.append(key, additionalData[key]);
      }
      fetch('../index.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(data => {
          console.log(data);
          alert('Data berhasil dikirim!');
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
  </script>
</body>

</html>