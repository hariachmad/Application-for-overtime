<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pengajuan Lembur</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .navbar {
            width: 20%;
            height: 100vh;
            background-color: #1e2538;
            color: white;
            padding: 1rem;
            position: fixed;
            left: 0;
            top: 0;
        }

        .content {
            margin-right: 20%; /* Sesuaikan dengan width navbar */
            padding: 20px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }

        .logo-image {
            height: 32px;
            width: auto;
        }

        .logo-text {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            opacity: 0.7;
        }

        .dropdown-icon {
            margin-left: auto;
            transition: transform 0.2s;
        }

        .nav-item.open .dropdown-icon {
            transform: rotate(180deg);
        }

        .submenu {
            list-style: none;
            margin-left: 2.5rem;
            height: 0;
            overflow: hidden;
            transition: height 0.3s ease-out;
        }

        .nav-item.open .submenu {
            height: auto;
        }

        .submenu-item {
            margin-bottom: 0.25rem;
        }

        .submenu-link {
            display: block;
            padding: 0.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s;
        }

        .submenu-link:hover {
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo-container">
            <img src="image/bluelake logo.png" class="logo-image">
            <span class="logo-text">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="list_pengajuan.php" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    List Pengajuan
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link" onclick="toggleSubmenu(this)">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Karyawan
                    <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </a>
                <ul class="submenu">
                    <li class="submenu-item"><a href="#" class="submenu-link">Data Karyawan</a></li>
                    <li class="submenu-item"><a href="#" class="submenu-link">Tambah Karyawan</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link" onclick="toggleSubmenu(this)">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Laporan
                    <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </a>
                <ul class="submenu">
                    <li class="submenu-item"><a href="#" class="submenu-link">Laporan Lembur</a></li>
                    <li class="submenu-item"><a href="#" class="submenu-link">Statistik Lembur</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Pengaturan
                </a>
            </li>
        </ul>
    </nav>

    <script>
        function toggleSubmenu(element) {
            const parent = element.parentElement;
            parent.classList.toggle('open');
        }
    </script>
</body>
</html>
