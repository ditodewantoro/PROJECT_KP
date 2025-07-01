<?php
// Memulai sesi PHP untuk mengelola status login pengguna
session_start();

// Menggabungkan file koneksi database
include 'koneksi.php';

// Memeriksa apakah pengguna sudah login (variabel sesi 'user_id' harus ada)
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login.php
    header('Location: login.php');
    exit(); // Hentikan eksekusi skrip lebih lanjut
}

// Memeriksa tingkat akses pengguna. Dashboard ini hanya untuk user_level 1 (pengguna biasa).
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 1) {
    // Jika tingkat akses tidak sesuai, tampilkan pesan alert dan arahkan kembali ke login.php
    echo "<script>alert('Role user tidak sesuai! Anda tidak memiliki akses ke halaman ini.'); window.location.href='login.php';</script>";
    exit(); // Hentikan eksekusi skrip
}

// Mengambil ID pengguna dari sesi
$user_id = $_SESSION['user_id'];
// Mengambil username dari sesi untuk ditampilkan di dashboard
$username = $_SESSION['username'];

// Query untuk mengambil semua data laptop yang tersedia
// Ini akan menampilkan semua laptop yang tersedia untuk pengguna
$laptop_result = mysqli_query($koneksi, "SELECT * FROM tbl_barang");

// Query UNTUK MENGAMBIL DATA PEMINJAMAN PENGGUNA YANG SUDAH DISETUJUI SAJA
// Menambahkan kondisi WHERE status = 'disetujui'
$peminjaman_result = mysqli_query($koneksi, "SELECT * FROM tbl_peminjaman 
                                            JOIN tbl_barang ON tbl_peminjaman.id_barang = tbl_barang.id_barang 
                                            WHERE tbl_peminjaman.pic_pinjam = $user_id AND tbl_peminjaman.status = 'disetujui'");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna | PT. Phapros Tbk</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" type="x-icon" href="assets/img/icon-removebg-preview.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="l-header" id="header">
        <nav class="nav bd-container" style="justify-content: space-between;">
            <a href="#" class="nav__logo">PT. Phapros Tbk</a>
            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item"><a href="#laptop" class="nav__link">Daftar Laptop</a></li>
                    <li class="nav__item"><a href="#peminjaman" class="nav__link">Peminjaman Saya</a></li>
                    <li class="nav__item"><a href="#contact" class="nav__link">Kontak</a></li>
                </ul>
            </div>
            <div class="account" style="font-size: 16px;">
                Selamat datang, <?php echo htmlspecialchars($username); ?>!
                <a href="logout.php" class="button" style="margin-left: 6px;">Logout</a>
            </div>
        </nav>
    </header>

    <main class="l-main">
        <section class="home" id="home">
            <div class="home__container bd-container bd-grid">
                <div class="home__data">
                    <h1 class="home__title">Selamat Datang di Portal Peminjaman Laptop</h1>
                    <h2 class="home__subtitle">Temukan laptop yang Anda butuhkan dan kelola peminjaman Anda.</h2>
                </div>
                <img src="assets/img/laptop.png" alt="Ilustrasi Laptop" class="home__img">
            </div>
        </section>

        <section class="product section bd-container" id="laptop">
            <span class="section-subtitle">Tersedia</span>
            <h2 class="section-title">Daftar Laptop</h2>
            <div class="products__container bd-grid">
                <?php
                // Memeriksa apakah ada hasil dari query laptop
                if (mysqli_num_rows($laptop_result) > 0) :
                    // Melakukan perulangan untuk setiap baris data laptop
                    while ($row = mysqli_fetch_assoc($laptop_result)) :
                ?>
                    <div class="products__content">
                        <img src="assets/img/laptop.png" alt="Gambar Laptop" class="products__img">
                        <h3 class="products__name"><?= htmlspecialchars($row['Merk_tipe']); ?></h3>
                        <span class="products__detail">
                            <?= htmlspecialchars($row['prosessor']); ?> | 
                            RAM: <?= htmlspecialchars($row['ram']); ?>GB | 
                            Storage: <?= htmlspecialchars($row['storage']); ?>GB (<?= htmlspecialchars($row['jenis_storage']); ?>)
                        </span><br>
                        <a href="tambah_peminjaman.php?id=<?= htmlspecialchars($row['id_barang']); ?>" class="button products__button">Pinjam</a>
                    </div>
                <?php 
                    endwhile;
                else :
                ?>
                    <p>Tidak ada laptop yang tersedia saat ini.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="product section bd-container" id="peminjaman">
            <span class="section-subtitle">Catatan Anda</span>
            <h2 class="section-title">Peminjaman Saya (Disetujui)</h2>
            <div class="products__container bd-grid">
                <?php
                // Memeriksa apakah ada hasil dari query peminjaman
                if (mysqli_num_rows($peminjaman_result) > 0) :
                    // Melakukan perulangan untuk setiap baris data peminjaman
                    while ($row = mysqli_fetch_assoc($peminjaman_result)) :
                ?>
                    <div class="products__content">
                        <h3 class="products__name"><?= htmlspecialchars($row['Merk_tipe']); ?></h3>
                        <span class="products__detail">
                            Keperluan: <?= htmlspecialchars($row['keperluan_pinjam']); ?> | 
                            Tanggal Pinjam: <?= htmlspecialchars($row['tgl_pinjam']); ?> | 
                            Tanggal Kembali: <?= htmlspecialchars($row['tgl_kembali']); ?>
                        </span><br>
                        </div>
                <?php 
                    endwhile;
                else :
                ?>
                    <p>Anda belum memiliki riwayat peminjaman yang disetujui.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="contact section bd-container" id="contact">
            <div class="contact__container bd-grid">
                <div class="contact__data">
                    <span class="section-subtitle contact__initial">Butuh Bantuan?</span>
                    <h2 class="section-title contact__initial">Hubungi Kami</h2>
                    <p class="contact__description">Jika Anda mengalami kendala atau memiliki pertanyaan terkait peminjaman laptop, silakan hubungi tim support kami.</p>
                </div>
                <div class="contact__button">
                    <a href="mailto:support@phapros.co.id" class="button">Kirim Email</a>
                    </div>
            </div>
        </section>
    </main>

    <footer class="footer section bd-container">
        <div class="footer__container bd-grid">
            <div class="footer__content">
                <a href="#" class="footer__logo">PT. Phapros Tbk</a>
                <span class="footer__description">Sistem Manajemen Laptop</span>
                <div>
                    <a href="#" class="footer__social"><i class='bx bxl-facebook'></i></a>
                    <a href="#" class="footer__social"><i class='bx bxl-instagram'></i></a>
                    <a href="#" class="footer__social"><i class='bx bxl-whatsapp'></i></a>
                </div>
            </div>
            </div>
        <p class="footer__copy">&#169; 2025 PT. Phapros Tbk. Semua hak dilindungi.</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>