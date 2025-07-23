<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] > 3) {
    echo "<script>alert('Role user tidak sesuai!'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pic_it = isset($_POST['pic_it']) ? (int) $_POST['pic_it'] : 0;
    $id_pinjam = isset($_POST['id_pinjam']) ? (int) $_POST['id_pinjam'] : 0;

    // Periksa apakah pic_it valid di tbl_user
    $stmt = $koneksi->prepare("SELECT id_user FROM tbl_user WHERE id_user = ?");
    $stmt->bind_param("i", $pic_it);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo "Error: PIC IT tidak ditemukan dalam tbl_user.";
        exit();
    }
    $stmt->close();

    // Periksa apakah id_pinjam valid di tbl_peminjaman
    $stmt = $koneksi->prepare("SELECT id_pinjam FROM tbl_peminjaman WHERE id_pinjam = ?");
    $stmt->bind_param("i", $id_pinjam);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo "Error: ID peminjaman tidak ditemukan.";
        exit();
    }
    $stmt->close();

    // Tentukan status berdasarkan tombol yang ditekan
    if (isset($_POST['approve'])) {
        $status = 'disetujui';
    } elseif (isset($_POST['reject'])) {
        $status = 'ditolak';
    } else {
        echo "Error: Aksi tidak valid.";
        exit();
    }

    // Gunakan prepared statement untuk update
    $stmt = $koneksi->prepare("UPDATE tbl_peminjaman SET status = ?, pic_it = ? WHERE id_pinjam = ?");
    $stmt->bind_param("sii", $status, $pic_it, $id_pinjam);

    if ($stmt->execute()) {
        header("Location: admin_panel.php");
        exit();
    } else {
        echo "Error SQL: " . $stmt->error;
    }

    $stmt->close();
}
?>
