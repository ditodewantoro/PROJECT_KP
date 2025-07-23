<?php
// Memulai sesi PHP
session_start();

// Menggabungkan file koneksi database
include 'koneksi.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Memeriksa tingkat akses pengguna. Hanya untuk user_level 2 (Admin IT/Peminjaman) atau level 3 (Administrator).
if (!isset($_SESSION['user_level']) || ($_SESSION['user_level'] != 2 && $_SESSION['user_level'] != 3)) {
    echo "<script>alert('Anda tidak memiliki akses untuk melakukan aksi ini.'); window.location.href='login.php';</script>";
    exit();
}

// Ambil user_level dari sesi
$user_level = $_SESSION['user_level'];

// Tentukan halaman pengalihan default
$redirect_url = 'login.php'; // Default fallback

// Tentukan halaman pengalihan berdasarkan user_level
if ($user_level == 2) {
    $redirect_url = 'admin_dashboard.php#peminjaman-management'; // Untuk Admin IT (level 2)
} elseif ($user_level == 3) {
    $redirect_url = 'admin_panel.php#peminjaman-management'; // Untuk Administrator (level 3)
}

if (isset($_GET['id_pinjam'])) {
    $id_pinjam = mysqli_real_escape_string($koneksi, $_GET['id_pinjam']);
    $pic_it = $_SESSION['user_id']; // ID PIC IT yang melakukan aksi adalah user yang sedang login

    // Gunakan prepared statement untuk update status menjadi 'selesai'
    // Pastikan hanya peminjaman yang berstatus 'disetujui' yang bisa diubah menjadi 'selesai'
    $stmt = $koneksi->prepare("UPDATE tbl_peminjaman SET status = 'selesai', pic_it = ? WHERE id_pinjam = ? AND status = 'disetujui'");
    $stmt->bind_param("ii", $pic_it, $id_pinjam); // 'ii' karena pic_it dan id_pinjam adalah integer

    if ($stmt->execute()) {
        echo "<script>alert('Peminjaman berhasil ditandai sebagai selesai. Laptop kini tersedia kembali.'); window.location.href='" . $redirect_url . "';</script>";
    } else {
        echo "<script>alert('Gagal menyelesaikan peminjaman: " . $stmt->error . "'); window.location.href='" . $redirect_url . "';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID Peminjaman tidak valid.'); window.location.href='" . $redirect_url . "';</script>";
}

mysqli_close($koneksi);
?>