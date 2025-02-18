<?php
include 'koneksi.php';
$result = mysqli_query($koneksi, "SELECT * FROM tbl_barang");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptop Management</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" type="x-icon" href="assets/img/icon-removebg-preview.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="l-header" id="header">
        <nav class="nav bd-container">
            <a href="#" class="nav__logo">PT. Phapros Tbk</a>
            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item"><a href="#home" class="nav__link">Home</a></li>
                    <li class="nav__item"><a href="#laptop" class="nav__link">Laptop</a></li>
                    <li class="nav__item"><a href="tambah_peminjaman.php" class="nav__link">Peminjaman Barang</a></li>
                    <li class="nav__item"><a href="#contact" class="nav__link">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="l-main">
        <!-- Banner -->
        <section class="home" id="home">
            <div class="home__container bd-container bd-grid">
                <div class="home__data">
                    <h1 class="home__title">Laptop Management</h1>
                    <h2 class="home__subtitle">Kelola daftar laptop dengan mudah dan cepat</h2>
                    <a href="tambah.php" class="button">Tambah Laptop</a>
                </div>
                <img src="assets/img/laptop.png" alt="" class="home__img">
            </div>
        </section>

        <!-- Daftar Laptop -->
        <section class="product section bd-container" id="laptop">
            <span class="section-subtitle">Data</span>
            <h2 class="section-title">Daftar Laptop</h2>

            <div class="products__container bd-grid">
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="products__content">
                    <img src="assets/img/laptop.png" alt="" class="products__img">
                    <h3 class="products__name"><?= $row['Merk_tipe']; ?></h3>
                    <span class="products__detail"><?= $row['prosessor']; ?> | RAM: <?= $row['ram']; ?>GB | Storage: <?= $row['storage']; ?>GB (<?= $row['jenis_storage']; ?>)</span><br>
                    <a href="edit.php?id=<?= $row['id_barang']; ?>" class="button products__button_detail">Edit</a>
                    <a href="hapus.php?id_barang=<?= $row['id_barang']; ?>" class="button products__button" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

   
            <!-- Menampilkan Daftar Peminjaman -->
        <section class="product section bd-container" id="peminjaman">
            <span class="section-subtitle">Data Peminjaman</span>
            <h2 class="section-title">Daftar Peminjaman</h2>

            <div class="products__container bd-grid">
                <?php
                $result = mysqli_query($koneksi, "SELECT * FROM tbl_peminjaman JOIN tbl_barang ON tbl_peminjaman.id_barang = tbl_barang.id_barang");
                while ($row = mysqli_fetch_assoc($result)) :
                ?>
                <div class="products__content">
                    <h3 class="products__name"><?= $row['Merk_tipe']; ?></h3>
                    <span class="products__detail"><?= $row['keperluan_pinjam']; ?> | Tanggal Pinjam: <?= $row['tgl_pinjam']; ?> | Tanggal Kembali: <?= $row['tgl_kembali']; ?></span><br>
                    <a href="edit_peminjaman.php?id_pinjam=<?= $row['id_pinjam']; ?>" class="button products__button_detail">Edit</a>
                    <a href="hapus_peminjaman.php?id_pinjam=<?= $row['id_pinjam']; ?>" class="button products__button" onclick="return confirm('Apakah Benar Selesai?')">Selesai</a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>


        <!-- Contact -->
        <section class="contact section bd-container" id="contact">
            <div class="contact__container bd-grid">
                <div class="contact__data">
                    <span class="section-subtitle contact__initial">Hubungi Kami</span>
                    <h2 class="section-title contact__initial">Contact us</h2>
                    <p class="contact__description">Jika ada kendala dalam sistem, silakan hubungi tim support kami.</p>
                </div>
                <div class="contact__button">
                    <a href="#" class="button">Hubungi Sekarang</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer section bd-container">
        <div class="footer__container bd-grid">
            <div class="footer__content">
                <a href="#" class="footer__logo">PT. Phapros Tbk</a>
                <span class="footer__description">Laptop Management</span>
                <a href="#" class="footer__social"><i class='bx bxl-facebook'></i></a>
                <a href="#" class="footer__social"><i class='bx bxl-instagram'></i></a>
                <a href="#" class="footer__social"><i class='bx bxl-whatsapp'></i></a>
            </div>
        </div>
        <p class="footer__copy">&#169; 2025 PT. Phapros Tbk</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
