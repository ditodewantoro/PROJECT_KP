<?php
include 'koneksi.php';

if (isset($_GET['id_barang'])) {
    $id_barang = $_GET['id_barang'];

    // Cek apakah barang sedang dipinjam
    $query_check = "SELECT * FROM tbl_peminjaman WHERE id_barang = $id_barang";
    $result_check = mysqli_query($koneksi, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Jika ada peminjaman, tampilkan notifikasi
        echo "<script>alert('Barang ini sedang dalam masa peminjaman dan tidak dapat dihapus!'); window.history.back();</script>";
    } else {
        // Jika tidak ada peminjaman, hapus barang
        $query_delete = "DELETE FROM tbl_barang WHERE id_barang = $id_barang";

        if (mysqli_query($koneksi, $query_delete)) {
            // Redirect ke halaman daftar barang setelah berhasil dihapus
            header("Location: admin_panel.php");
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>
