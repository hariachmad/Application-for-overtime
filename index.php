<?php
ob_start();
session_start();
require_once 'config.php';

// Initialize error message variable
$error_message = "";

// Function to sanitize input
function sanitize_input($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}


// Function to check user credentials
function check_credentials($conn, $table, $username, $password) {
    $sql = "SELECT * FROM $table WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return false;
}

// Process login when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // No need to sanitize to avoid altering password characters

    if (empty($username) || empty($password)) {
        $error_message = "Username dan password harus diisi!";
    } else {
        error_log("Login attempt for username: " . $username);

        // Check admin login
        $admin = check_credentials($conn, 'admin', $username, $password);
        if ($admin) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $admin['admin_id'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];

            header('Location: admin/index.php');
            exit();
        }

        // Check employee login
        $karyawan = check_credentials($conn, 'karyawan', $username, $password);
        if ($karyawan) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $karyawan['karyawan_id'];
            $_SESSION['user_type'] = 'karyawan';
            $_SESSION['username'] = $karyawan['username'];

            header("Location: user_base.php");
            exit();
        }

        $error_message = "Username atau password salah!";
        error_log("Authentication failed for username: " . $username);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlueLake - Sign In</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: #1a1f2e;
        }

        .logo-container {
            margin-bottom: 2rem;
            text-align: center;
        }

        .logo-text {
            color: #ffffff;
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-image {
            height: 32px;
            width: auto;
            margin-right: 8px;
        }

        .signin-container {
            background-color: #1e2538;
            padding: 2rem;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: #fff;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            background-color: #2a3142;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 1rem;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-group input:focus {
            outline: none;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        .password-input-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
        }

        .sign-in-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .sign-in-button:hover {
            background-color: #2563eb;
        }

        .error-message {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        </style>
</head>
<body>
    <div class="logo-container">
        <div class="logo-text">
            <img src="admin/image/bluelake logo.png" alt="BlueLake Logo" class="logo-image">
            BlueLake
        </div>
    </div>

    <div class="signin-container">
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        👁️
                    </button>
                </div>
            </div>

            <button type="submit" class="sign-in-button">Sign In</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>
</html>