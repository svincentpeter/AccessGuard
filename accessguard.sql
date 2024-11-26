-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Nov 2024 pada 07.02
-- Versi server: 10.4.25-MariaDB
-- Versi PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `accessguard`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `activity_description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `activity_type`, `activity_description`, `timestamp`) VALUES
(1, 1, 'Login', 'Pengguna login ke sistem', '2024-11-01 04:54:16'),
(2, 2, 'Tambah Pengguna', 'Pengguna menambahkan pengguna baru dengan username admin_user', '2024-11-01 04:54:16'),
(3, 4, 'Logout', 'Pengguna logout dari sistem', '2024-11-01 04:54:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL COMMENT 'ID dari role pengguna',
  `module_name` varchar(50) NOT NULL COMMENT 'Nama modul seperti Dashboard, Manajemen User, Log Aktivitas, dll.',
  `can_create` tinyint(1) DEFAULT 0 COMMENT 'Izin untuk membuat',
  `can_read` tinyint(1) DEFAULT 1 COMMENT 'Izin untuk membaca',
  `can_update` tinyint(1) DEFAULT 0 COMMENT 'Izin untuk mengedit',
  `can_delete` tinyint(1) DEFAULT 0 COMMENT 'Izin untuk menghapus'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `role_id`, `module_name`, `can_create`, `can_read`, `can_update`, `can_delete`) VALUES
(1, 1, 'Dashboard', 1, 1, 1, 1),
(2, 1, 'Manajemen User', 1, 1, 1, 1),
(3, 1, 'Log Aktivitas', 1, 1, 1, 1),
(4, 1, 'Laporan', 1, 1, 1, 1),
(5, 2, 'Dashboard', 0, 1, 0, 0),
(6, 2, 'Manajemen User', 1, 1, 1, 1),
(7, 2, 'Log Aktivitas', 0, 1, 0, 0),
(8, 2, 'Laporan', 0, 1, 0, 0),
(9, 3, 'Dashboard', 0, 1, 0, 0),
(10, 3, 'Manajemen User', 0, 0, 0, 0),
(11, 3, 'Log Aktivitas', 0, 0, 0, 0),
(12, 3, 'Laporan', 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL COMMENT 'Nama peran, seperti Root, Staf Administrasi, Pimpinan',
  `description` text DEFAULT NULL COMMENT 'Deskripsi singkat peran'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'Root', 'Akses penuh ke semua modul dan data dalam sistem.'),
(2, 'Admin', 'Dapat menginput, melihat, mengedit, dan menghapus data tertentu.'),
(3, 'Pimpinan', 'Hanya dapat melihat laporan tanpa hak untuk menginput data.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL COMMENT 'Nama pengguna',
  `email` varchar(100) NOT NULL COMMENT 'Email pengguna',
  `password` varchar(255) NOT NULL COMMENT 'Kata sandi hashed',
  `role_id` int(11) NOT NULL COMMENT 'ID dari role pengguna',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Waktu pembuatan akun',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Waktu terakhir data diperbarui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'root', 'root@example.com', '482c811da5d5b4bc6d497ffa98491e38', 1, '2024-11-01 03:21:02', '2024-11-01 03:21:02'),
(2, 'admin', 'admin@example.com', '482c811da5d5b4bc6d497ffa98491e38', 2, '2024-11-01 03:21:02', '2024-11-01 03:21:02'),
(4, 'mbul', 'mbul@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 3, '2024-11-01 04:15:58', '2024-11-01 04:16:11'),
(5, 'cathie', 'cathie@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 2, '2024-11-01 14:12:01', '2024-11-01 14:12:01'),
(6, 'admin_mbul', 'bulbul@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 2, '2024-11-01 14:28:55', '2024-11-01 14:28:55');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
