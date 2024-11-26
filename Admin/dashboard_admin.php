<?php
session_start();
include '../dbconfig.php';

// Validasi untuk memastikan hanya pengguna dengan role "Admin" yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
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
    <title>Dashboard Admin - AccessGuard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js untuk grafik -->
</head>
<body>

<div class="container mt-4">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, Anda memiliki hak akses untuk mengelola pengguna tertentu di sistem ini.</p>

    <div class="row">
        <!-- Ringkasan Jumlah Pengguna Berdasarkan Role -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Ringkasan Jumlah Pengguna Berdasarkan Role</div>
                <div class="card-body">
                    <canvas id="userSummaryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Daftar Pengguna yang Dikelola -->
        <div class="col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header">Daftar Pengguna yang Dikelola</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query untuk mendapatkan daftar pengguna yang bukan Admin
                            $userQuery = "SELECT users.id, users.username, users.email, roles.role_name 
              FROM users 
              JOIN roles ON users.role_id = roles.id 
              WHERE roles.role_name != 'Admin'";
$userResult = $conn->query($userQuery);


                            while ($user = $userResult->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']); ?></td>
                                    <td><?= htmlspecialchars($user['email']); ?></td>
                                    <td><?= htmlspecialchars($user['role_name']); ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?= $user['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="delete_user.php?id=<?= $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk menampilkan chart Ringkasan Jumlah Pengguna -->
<script>
// Fungsi untuk fetch data jumlah pengguna berdasarkan role
fetch('dashboard_admin.php?report_type=user_summary')
    .then(response => response.json())
    .then(data => {
        console.log(data); // Debug untuk memastikan data tidak kosong
        const ctxUserSummary = document.getElementById('userSummaryChart').getContext('2d');
        new Chart(ctxUserSummary, {
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

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
if (isset($_GET['report_type']) && $_GET['report_type'] == 'user_summary') {
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
