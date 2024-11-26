<?php
session_start();
include '../dbconfig.php';

// Validasi untuk memastikan hanya pengguna dengan role "Pimpinan" yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Pimpinan') {
    header("Location: ../index.php?error=Unauthorized Access");
    exit();
}

include '../navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pimpinan - AccessGuard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js untuk grafik -->
</head>
<body>

<div class="container mt-4">
    <h2>Dashboard Pimpinan</h2>
    <p>Selamat datang, Anda memiliki akses untuk melihat laporan dan ringkasan data dalam sistem ini.</p>

    <div class="row">
        <!-- Ringkasan Statistik Pengguna Berdasarkan Role -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Ringkasan Statistik Pengguna Berdasarkan Role</div>
                <div class="card-body">
                    <canvas id="roleSummaryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Laporan Aktivitas Pengguna -->
        <div class="col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header">Laporan Aktivitas Pengguna</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Jenis Aktivitas</th>
                                <th>Deskripsi</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query untuk mendapatkan data log aktivitas pengguna
                            $logQuery = "SELECT users.username, activity_log.activity_type, activity_log.activity_description, activity_log.timestamp 
                                         FROM activity_log 
                                         JOIN users ON activity_log.user_id = users.id 
                                         ORDER BY activity_log.timestamp DESC 
                                         LIMIT 10"; // Batas 10 entri terbaru
                            $logResult = $conn->query($logQuery);

                            if ($logResult->num_rows > 0) {
                                while ($log = $logResult->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($log['username']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['activity_type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['activity_description']) . "</td>";
                                    echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Tidak ada aktivitas yang ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk menampilkan chart Ringkasan Statistik Pengguna -->
<script>
// Fungsi untuk fetch data statistik pengguna berdasarkan role
fetch('dashboard_pemimpin.php?report_type=role_summary')
    .then(response => response.json())
    .then(data => {
        const ctxRoleSummary = document.getElementById('roleSummaryChart').getContext('2d');
        new Chart(ctxRoleSummary, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.counts,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                }]
            }
        });
    })
    .catch(error => console.error('Error fetching data:', error));
</script>

<!-- Bootstrap JS, Popper.js, dan jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Menangani permintaan data untuk Ringkasan Statistik Pengguna Berdasarkan Role
if (isset($_GET['report_type']) && $_GET['report_type'] == 'role_summary') {
    $data = [];
    $query = "SELECT roles.role_name, COUNT(users.id) AS total_users
              FROM users
              JOIN roles ON users.role_id = roles.id
              GROUP BY roles.role_name";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $data['labels'][] = $row['role_name'];
        $data['counts'][] = $row['total_users'];
    }

    echo json_encode($data);
    exit();
}

$conn->close();
?>
