<?php
include 'koneksi.php';

// Ambil id_user dari session yang login
session_start();
$user_id = $_SESSION['id_user'];

// Query untuk mengambil data laptop yang tersedia
$laptop_result = mysqli_query($koneksi, "SELECT * FROM tbl_barang");

// Query untuk mengambil data peminjaman pengguna yang login
$peminjaman_result = mysqli_query($koneksi, "SELECT * FROM tbl_peminjaman JOIN tbl_barang ON tbl_peminjaman.id_barang = tbl_barang.id_barang WHERE tbl_peminjaman.id_user = $user_id");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | PT. Phapros Tbk</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" type="x-icon" href="assets/img/icon-removebg-preview.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- User Dashboard Content -->
    <main class="l-main">
        <section class="product section bd-container" id="laptop">
            <h2 class="section-title">Daftar Laptop</h2>
            <div class="products__container bd-grid">
                <?php while ($row = mysqli_fetch_assoc($laptop_result)) : ?>
                <div class="products__content">
                    <img src="assets/img/laptop.png" alt="" class="products__img">
                    <h3 class="products__name"><?= $row['Merk_tipe']; ?></h3>
                    <span class="products__detail"><?= $row['prosessor']; ?> | RAM: <?= $row['ram']; ?>GB | Storage: <?= $row['storage']; ?>GB (<?= $row['jenis_storage']; ?>)</span><br>
                    <a href="tambah_peminjaman.php?id=<?= $row['id_barang']; ?>" class="button products__button">Pinjam</a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="product section bd-container" id="peminjaman">
            <h2 class="section-title">Peminjaman Saya</h2>
            <div class="products__container bd-grid">
                <?php while ($row = mysqli_fetch_assoc($peminjaman_result)) : ?>
                <div class="products__content">
                    <h3 class="products__name"><?= $row['Merk_tipe']; ?></h3>
                    <span class="products__detail">Keperluan: <?= $row['keperluan_pinjam']; ?> | Tanggal Pinjam: <?= $row['tgl_pinjam']; ?> | Tanggal Kembali: <?= $row['tgl_kembali']; ?></span><br>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>
</body>
</html>
