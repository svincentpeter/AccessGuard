<?php
session_start(); // Memulai session

// Menghapus semua session
session_unset(); // Menghapus variabel session
session_destroy(); // Menghancurkan session

// Mengarahkan pengguna kembali ke halaman login dengan pesan sukses
header("Location: index.php?message=Logout berhasil");
exit();
?>
