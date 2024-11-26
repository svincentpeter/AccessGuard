<?php
session_start();
include '../dbconfig.php';

// Pastikan hanya pengguna dengan role 'Root' atau yang berhak yang bisa mengakses halaman ini
if ($_SESSION['role'] != 'Root' && $_SESSION['role'] != 'Pimpinan') {
    header("Location: ../index.php?error=Unauthorized Access");
    exit();
}

// Fungsi untuk mendapatkan data berdasarkan jenis laporan
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
    exit(); // Menghentikan script agar hanya mengirimkan data JSON saat request AJAX
}

include '../navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aktivitas - AccessGuard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js untuk grafik -->
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">Laporan Sistem AccessGuard</h2>

    <div class="row">
        <!-- Ringkasan Pengguna Berdasarkan Role -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Ringkasan Pengguna Berdasarkan Role</div>
                <div class="card-body">
                    <canvas id="roleChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Laporan Aktivitas Harian -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Laporan Aktivitas Harian</div>
                <div class="card-body">
                    <canvas id="dailyActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Laporan Aktivitas Berdasarkan Jenis -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Laporan Aktivitas Berdasarkan Jenis</div>
                <div class="card-body">
                    <canvas id="activityTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pengguna Paling Aktif -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Pengguna Paling Aktif</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Jumlah Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query untuk mendapatkan pengguna paling aktif
                            $activeUserQuery = "SELECT users.username, COUNT(activity_log.id) AS activity_count
                                                FROM activity_log
                                                JOIN users ON activity_log.user_id = users.id
                                                GROUP BY users.username
                                                ORDER BY activity_count DESC
                                                LIMIT 10";
                            $activeUserResult = $conn->query($activeUserQuery);
                            
                            while ($row = $activeUserResult->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td><?= htmlspecialchars($row['activity_count']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk mendapatkan data dari server dan menampilkan grafik -->
<script>
// Fungsi untuk fetch data berdasarkan jenis laporan
function fetchData(reportType, callback) {
    fetch(`report.php?report_type=${reportType}`)
        .then(response => response.json())
        .then(data => callback(data))
        .catch(error => console.error('Error fetching data:', error));
}

// Menampilkan chart Ringkasan Pengguna Berdasarkan Role
fetchData('role_summary', function(data) {
    const ctxRole = document.getElementById('roleChart').getContext('2d');
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

// Menampilkan chart Laporan Aktivitas Harian
fetchData('daily_activity', function(data) {
    const ctxDaily = document.getElementById('dailyActivityChart').getContext('2d');
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

// Menampilkan chart Laporan Aktivitas Berdasarkan Jenis
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
$conn->close();
?>
