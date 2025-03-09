<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang = $_POST['id_barang'];
    $keperluan_pinjam = $_POST['keperluan_pinjam'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];
    
    $pic_pinjam = $_SESSION['user_id'];

    // Menghitung durasi pinjam (selisih antara tgl_kembali dan tgl_pinjam)
    $tgl_pinjam_date = new DateTime($tgl_pinjam);
    $tgl_kembali_date = new DateTime($tgl_kembali);
    $interval = $tgl_pinjam_date->diff($tgl_kembali_date);
    $durasi_pinjam = $interval->format('%a');  // Mengambil jumlah hari

    // Query untuk insert data peminjaman
    $query = "INSERT INTO tbl_peminjaman (id_barang, keperluan_pinjam, tgl_pinjam, durasi_pinjam, tgl_kembali, pic_pinjam)
            VALUES ('$id_barang', '$keperluan_pinjam', '$tgl_pinjam', '$durasi_pinjam', '$tgl_kembali', '$pic_pinjam')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: user_dashboard.php"); // Redirect ke halaman utama setelah peminjaman berhasil
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
