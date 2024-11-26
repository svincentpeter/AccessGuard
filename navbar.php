<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role'])) {
    header("Location: index.php?error=Silakan login terlebih dahulu");
    exit();
}
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AccessGuard</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Warna Palet */
        :root {
            --pink-pastel: #FFC0CB;
            --pink-dark: #FF91A4;
            --yellow-pastel: #FFFACD;
            --text-color: #333333;
        }

        /* Navbar Kustom */
        .navbar-custom {
            background-color: var(--pink-pastel); /* Warna latar navbar */
        }
        .navbar-custom .navbar-brand {
            font-weight: bold;
            color: var(--text-color);
        }
        .navbar-custom .navbar-brand:hover {
            color: var(--pink-dark);
        }
        .navbar-custom .nav-link {
            color: var(--text-color) !important;
            font-weight: bold;
        }
        .navbar-custom .nav-link:hover {
            background-color: var(--yellow-pastel);
            color: var(--text-color) !important;
            border-radius: 10px;
        }
        .navbar-custom .nav-item.active .nav-link {
            background-color: var(--pink-dark);
            color: white !important;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <a class="navbar-brand" href="#">AccessGuard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if ($role == 'Root'): ?>
                <li class="nav-item"><a class="nav-link" href="dashboard_root.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="user_management.php">Manajemen User</a></li>
                <li class="nav-item"><a class="nav-link" href="activity_log.php">Log Aktivitas</a></li>
                <li class="nav-item"><a class="nav-link" href="report.php">Laporan</a></li>
            <?php elseif ($role == 'Admin'): ?>
                <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="user_management.php">Manajemen User</a></li>
                <li class="nav-item"><a class="nav-link" href="report.php">Laporan</a></li>
            <?php elseif ($role == 'Pimpinan'): ?>
                <li class="nav-item"><a class="nav-link" href="dashboard_pimpinan.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="report.php">Laporan</a></li>
            <?php endif; ?>
            <!-- Menu umum untuk semua role -->
            <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
            <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
