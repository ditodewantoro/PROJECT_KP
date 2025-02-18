<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pinjam = $_POST['id_pinjam'];
    $id_barang = $_POST['id_barang'];
    $keperluan_pinjam = $_POST['keperluan_pinjam'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];
    $pic_it = $_POST['pic_it'];
    $pic_pinjam = $_POST['pic_pinjam'];

    // Query untuk update data peminjaman
    $query = "UPDATE tbl_peminjaman
              SET id_barang = '$id_barang', keperluan_pinjam = '$keperluan_pinjam', tgl_pinjam = '$tgl_pinjam', tgl_kembali = '$tgl_kembali', pic_it = '$pic_it', pic_pinjam = '$pic_pinjam'
              WHERE id_pinjam = $id_pinjam";

    if (mysqli_query($koneksi, $query)) {
        header("Location: index.php"); // Kembali ke halaman utama setelah update berhasil
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
