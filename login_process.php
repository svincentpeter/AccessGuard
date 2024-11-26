<?php
session_start();
include 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash password sesuai yang ada di database

    // Query untuk mengambil data pengguna berdasarkan username dan password
    $query = "SELECT users.id, users.username, users.role_id, roles.role_name 
              FROM users 
              JOIN roles ON users.role_id = roles.id 
              WHERE users.username = ? AND users.password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Set session berdasarkan data yang diperoleh dari database
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name']; // Simpan nama role ke dalam session
        
        // Debug untuk memeriksa isi session
        // var_dump($_SESSION); exit(); // Hapus setelah debugging selesai
        
        // Redirect ke halaman sesuai role
        if ($user['role_name'] == 'Root') {
            header("Location: Root/dashboard_root.php");
        } elseif ($user['role_name'] == 'Admin') {
            header("Location: Admin/dashboard_admin.php");
        } elseif ($user['role_name'] == 'Pimpinan') {
            header("Location: Pimpinan/dashboard_pimpinan.php");
        } else {
            header("Location: index.php?error=Unauthorized Access");
        }
        exit();
    } else {
        header("Location: index.php?error=Username atau password salah");
        exit();
    }
}
?>
