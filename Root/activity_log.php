<?php
session_start();
include '../dbconfig.php';

// Pastikan hanya pengguna dengan role 'Root' yang bisa mengakses halaman ini
if ($_SESSION['role'] != 'Root') {
    header("Location: ../index.php?error=Unauthorized Access");
    exit();
}

// Ambil data log aktivitas dari database, termasuk role pengguna
$query = "SELECT activity_log.id, users.username, roles.role_name, activity_log.activity_type, activity_log.activity_description, activity_log.timestamp 
          FROM activity_log 
          JOIN users ON activity_log.user_id = users.id 
          JOIN roles ON users.role_id = roles.id
          ORDER BY activity_log.timestamp DESC";
$result = $conn->query($query);

// Cek jika query gagal dan tampilkan pesan error
if (!$result) {
    die("Error dalam query: " . $conn->error);
}

include '../navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - AccessGuard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS Timeline yang diberikan */
        body {
            background-color: #f9f9fa;
        }
        .page-container {
            max-width: 1140px;
            margin: 0 auto;
        }
        .padding {
            padding: 2rem;
        }
        .timeline {
            position: relative;
            border-color: rgba(160, 175, 185, .15);
            padding: 0;
            margin: 0;
        }
        .tl-item {
            border-radius: 3px;
            position: relative;
            display: flex;
        }
        .tl-item > * {
            padding: 10px;
        }
        .tl-dot {
            position: relative;
            border-color: rgba(160, 175, 185, .15);
        }
        .tl-dot:after,
        .tl-dot:before {
            content: '';
            position: absolute;
            border-color: inherit;
            border-width: 2px;
            border-style: solid;
            border-radius: 50%;
            width: 10px;
            height: 10px;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
        }
        .tl-dot:after {
            width: 0;
            height: auto;
            top: 25px;
            bottom: -15px;
            border-right-width: 0;
            border-top-width: 0;
            border-bottom-width: 0;
            border-radius: 0;
        }
        .b-root { border-color: #0000FF !important; } /* Warna biru untuk Root */
        .b-admin { border-color: #FF4500 !important; } /* Warna oranye untuk Admin */
        .b-staff { border-color: #32CD32 !important; } /* Warna hijau untuk Staf Administrasi */
        .b-leader { border-color: #FFD700 !important; } /* Warna emas untuk Pimpinan */
        .tl-content .tl-date {
            font-size: .85em;
            margin-top: 2px;
            min-width: 100px;
            max-width: 100px;
        }
    </style>
</head>
<body>

<div class="page-content page-container" id="page-content">
    <div class="padding">
        <h2 class="mb-4">Log Aktivitas</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <!-- Timeline Log Aktivitas -->
            <div class="timeline p-4 block mb-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="tl-item">
                        <!-- Menentukan warna dot berdasarkan role pengguna -->
                        <?php
                            $role = strtolower($row['role_name']);
                            switch ($role) {
                                case 'root':
                                    $dotClass = 'b-root';
                                    break;
                                case 'admin':
                                    $dotClass = 'b-admin';
                                    break;
                                case 'staf administrasi':
                                    $dotClass = 'b-staff';
                                    break;
                                case 'pimpinan':
                                    $dotClass = 'b-leader';
                                    break;
                                default:
                                    $dotClass = 'b-primary'; // Default color jika role tidak dikenali
                                    break;
                            }
                        ?>
                        <div class="tl-dot <?= $dotClass; ?>"></div>
                        <div class="tl-content">
                            <div class="">
                                <strong><?= htmlspecialchars($row['username']); ?> (<?= htmlspecialchars($row['role_name']); ?>):</strong> 
                                <?= htmlspecialchars($row['activity_description']); ?>
                            </div>
                            <div class="tl-date text-muted mt-1"><?= htmlspecialchars($row['timestamp']); ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="alert alert-info">Tidak ada log aktivitas yang ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Resource JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
