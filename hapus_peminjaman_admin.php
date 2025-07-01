<?php
include 'koneksi.php';

if (isset($_GET['id_pinjam'])) {
    $id_pinjam = $_GET['id_pinjam'];

    // Hapus peminjaman berdasarkan id_pinjam terlebih dahulu
    $query_delete_peminjaman = "DELETE FROM tbl_peminjaman WHERE id_pinjam = $id_pinjam";

    if (mysqli_query($koneksi, $query_delete_peminjaman)) {
        // Jika peminjaman berhasil dihapus, arahkan ke halaman daftar peminjaman
        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
