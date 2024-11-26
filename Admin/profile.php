<?php
session_start();
include '../dbconfig.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu");
    exit();
}

// Dapatkan ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Query untuk mendapatkan informasi profil pengguna berdasarkan user_id
$query = "SELECT users.username, users.email, roles.role_name, users.created_at 
          FROM users 
          JOIN roles ON users.role_id = roles.id 
          WHERE users.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses Update Profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];

        // Update informasi username dan email
        $updateQuery = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssi", $username, $email, $user_id);
        $stmt->execute();

        // Refresh halaman setelah update
        header("Location: profil.php?success=Profil berhasil diperbarui");
        exit();
    }

    // Proses ubah kata sandi
    if (isset($_POST['change_password'])) {
        $current_password = md5($_POST['current_password']);
        $new_password = md5($_POST['new_password']);

        // Verifikasi kata sandi saat ini
        $passwordQuery = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($passwordQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['password'] === $current_password) {
            // Update kata sandi baru
            $updatePasswordQuery = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($updatePasswordQuery);
            $stmt->bind_param("si", $new_password, $user_id);
            $stmt->execute();

            header("Location: profil.php?success=Kata sandi berhasil diubah");
            exit();
        } else {
            header("Location: profil.php?error=Kata sandi saat ini salah");
            exit();
        }
    }
}

include '../navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - AccessGuard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>Profil Pengguna</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <!-- Informasi Profil -->
    <div class="card mb-4">
        <div class="card-header">Informasi Dasar</div>
        <div class="card-body">
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($user['role_name']); ?></p>
            <p><strong>Tanggal Pendaftaran:</strong> <?= htmlspecialchars($user['created_at']); ?></p>
        </div>
    </div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
