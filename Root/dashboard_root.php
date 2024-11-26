<?php
session_start();
include '../dbconfig.php';

// Pastikan hanya pengguna dengan role 'Root' yang bisa mengakses halaman ini
if ($_SESSION['role'] != 'Root') {
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
    <title>Dashboard Root - AccessGuard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js untuk grafik -->
</head>
<body>

<div class="container mt-4">
    <h2>Dashboard Root</h2>
    <p>Selamat datang, Anda memiliki akses penuh ke semua data dan pengaturan sistem.</p>

    <div class="row">
        <!-- Ringkasan Pengguna Berdasarkan Role -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Ringkasan Pengguna Berdasarkan Role</div>
                <div class="card-body">
                    <canvas id="roleSummaryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Log Aktivitas Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Log Aktivitas Terbaru</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                        $logQuery = "SELECT users.username, activity_log.activity_type, activity_log.timestamp 
                                     FROM activity_log 
                                     JOIN users ON activity_log.user_id = users.id 
                                     ORDER BY activity_log.timestamp DESC 
                                     LIMIT 5";
                        $logResult = $conn->query($logQuery);

                        if ($logResult->num_rows > 0) {
                            while ($log = $logResult->fetch_assoc()) {
                                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                                echo "<div><strong>" . htmlspecialchars($log['username']) . "</strong> - " . htmlspecialchars($log['activity_type']) . "</div>";
                                echo "<span class='badge badge-secondary'>" . htmlspecialchars($log['timestamp']) . "</span>";
                                echo "</li>";
                            }
                        } else {
                            echo "<li class='list-group-item'>Tidak ada aktivitas terbaru.</li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistik Akses Harian -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Statistik Akses Harian</div>
                <div class="card-body">
                    <canvas id="dailyAccessChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Laporan Berdasarkan Jenis Aktivitas -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Laporan Berdasarkan Jenis Aktivitas</div>
                <div class="card-body">
                    <canvas id="activityTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk mendapatkan data dari server dan menampilkan grafik -->
<script>
// Fungsi untuk fetch data berdasarkan jenis laporan
function fetchData(reportType, callback) {
    fetch(`dashboard_root.php?report_type=${reportType}`)
        .then(response => response.json())
        .then(data => callback(data))
        .catch(error => console.error('Error fetching data:', error));
}

// Menampilkan chart Ringkasan Pengguna Berdasarkan Role
fetchData('role_summary', function(data) {
    const ctxRole = document.getElementById('roleSummaryChart').getContext('2d');
    new Chart(ctxRole, {
        type: 'pie',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.counts,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
            }]
        }
    });
});

// Menampilkan chart Statistik Akses Harian
fetchData('daily_activity', function(data) {
    const ctxDaily = document.getElementById('dailyAccessChart').getContext('2d');
    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: data.dates,
            datasets: [{
                label: 'Aktivitas Harian',
                data: data.counts,
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                fill: true,
            }]
        }
    });
});

// Menampilkan chart Laporan Berdasarkan Jenis Aktivitas
fetchData('activity_type', function(data) {
    const ctxType = document.getElementById('activityTypeChart').getContext('2d');
    new Chart(ctxType, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Jumlah Aktivitas',
                data: data.counts,
                backgroundColor: '#36b9cc',
            }]
        }
    });
});
</script>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Menangani permintaan data berdasarkan parameter `report_type`
if (isset($_GET['report_type'])) {
    $reportType = $_GET['report_type'];
    $data = [];

    if ($reportType == 'role_summary') {
        $query = "SELECT roles.role_name, COUNT(users.id) AS total_users
                  FROM users
                  JOIN roles ON users.role_id = roles.id
                  GROUP BY roles.role_name";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $data['labels'][] = $row['role_name'];
            $data['counts'][] = $row['total_users'];
        }
    } elseif ($reportType == 'daily_activity') {
        $query = "SELECT DATE(timestamp) AS date, COUNT(id) AS activity_count
                  FROM activity_log
                  GROUP BY DATE(timestamp)
                  ORDER BY DATE(timestamp)";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $data['dates'][] = $row['date'];
            $data['counts'][] = $row['activity_count'];
        }
    } elseif ($reportType == 'activity_type') {
        $query = "SELECT activity_type, COUNT(id) AS activity_count
                  FROM activity_log
                  GROUP BY activity_type";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $data['labels'][] = $row['activity_type'];
            $data['counts'][] = $row['activity_count'];
        }
    }

    echo json_encode($data);
    exit();
}

$conn->close();
?>
