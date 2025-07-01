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

// Query UNTUK MENGAMBIL DATA PEMINJAMAN PENGGUNA DENGAN SEMUA STATUS
// Menghapus kondisi WHERE status = 'disetujui' agar semua status ditampilkan
$peminjaman_query = "SELECT
                        p.id_pinjam,
                        b.Merk_tipe,
                        b.prosessor,
                        b.ram,
                        b.storage,
                        b.jenis_storage,
                        p.keperluan_pinjam,
                        p.tgl_pinjam,
                        p.tgl_kembali,
                        p.status -- Tambahkan kolom status untuk diambil
                    FROM tbl_peminjaman p
                    JOIN tbl_barang b ON p.id_barang = b.id_barang
                    WHERE p.pic_pinjam = '$user_id'
                    ORDER BY p.tgl_pinjam DESC"; // Urutkan berdasarkan tanggal pinjam terbaru

$peminjaman_result = mysqli_query($koneksi, $peminjaman_query);

// Cek jika query gagal (untuk debugging)
if (!$peminjaman_result) {
    die("Query Peminjaman Gagal: " . mysqli_error($koneksi));
}

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
    <style>
        /* CSS Tambahan untuk User Dashboard (Badge Status) */

        /* Styling untuk badge status peminjaman (diambil dari admin dashboard, disesuaikan agar mandiri) */
        .status-badge {
            padding: 5px 10px; /* Disesuaikan agar mirip dengan tombol di layout Anda */
            border-radius: 4px; /* Sudut sedikit membulat */
            font-size: 0.85em; /* Ukuran font lebih kecil */
            font-weight: bold;
            color: white;
            margin-top: 0.75rem; /* Jarak dari detail di atasnya */
            display: inline-block; /* Agar bisa diatur padding/margin */
            text-align: center; /* Teks di tengah badge */
            width: fit-content; /* Lebar sesuai konten */
            min-width: 80px; /* Lebar minimum untuk konsistensi */
        }
        .status-badge.pending { background-color: #ffc107; color: #343a40; } /* Kuning untuk pending */
        .status-badge.disetujui { background-color: #28a745; } /* Hijau untuk disetujui */
        .status-badge.ditolak { background-color: #dc3545; } /* Merah untuk ditolak */
        .status-badge.selesai { background-color: #6c757d; } /* Abu-abu untuk selesai */

        /* Perbaikan jika padding-top main content belum ada di style.css */
        .l-main {
            padding-top: var(--header-height, 60px); /* Sesuaikan dengan tinggi header Anda */
        }

        /* Jika .products__content memiliki display: flex yang membuat item sejajar */
        .products__container {
            display: grid; /* Gunakan grid untuk tata letak yang lebih fleksibel */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Kolom responsif */
            gap: 1.5rem; /* Jarak antar item */
            justify-content: center; /* Pusatkan item jika kurang dari grid penuh */
        }
        .products__content {
            display: flex; /* Aktifkan flexbox untuk konten di dalam card */
            flex-direction: column; /* Tata letak vertikal */
            align-items: center; /* Pusatkan item secara horizontal */
            text-align: center; /* Pastikan teks di tengah */
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .products__content:hover {
            transform: translateY(-5px);
        }
        .products__name {
            font-size: 1.2em;
            margin-bottom: 0.5rem;
        }
        .products__detail {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 1rem;
        }
        .products__img {
            max-width: 80px; /* Ukuran gambar dalam card */
            height: auto;
            margin-bottom: 1rem;
        }
        .products__button {
            margin-top: 1rem;
        }

        /* Gaya untuk footer agar responsif dan bersih */
        .footer__container {
            display: flex;
            flex-wrap: wrap; /* Izinkan wrap pada layar kecil */
            justify-content: center; /* Pusatkan konten */
            align-items: center;
            padding: 1.5rem 0;
        }
        .footer__content {
            text-align: center;
            margin: 0 1rem;
        }
        .footer__social {
            display: inline-block;
            margin: 0 0.5rem;
            font-size: 1.5rem;
            color: #fff;
        }
        .footer__copy {
            width: 100%; /* Pastikan copyright di baris baru */
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <header class="l-header" id="header">
        <nav class="nav bd-container" style="justify-content: space-between;">
            <a href="#" class="nav__logo">PT. Phapros Tbk</a>
            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item"><a href="#home" class="nav__link">Beranda</a></li>
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
            <h2 class="section-title">Peminjaman Saya</h2>
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
                            Keperluan: <?= htmlspecialchars($row['keperluan_pinjam']); ?><br>
                            Tgl Pinjam: <?= htmlspecialchars($row['tgl_pinjam']); ?><br>
                            Tgl Kembali: <?= htmlspecialchars($row['tgl_kembali']); ?>
                        </span>
                        <?php
                        // Menampilkan status peminjaman dengan badge
                        $status_class = '';
                        $status_text = ucfirst(htmlspecialchars($row['status']));
                        switch ($row['status']) {
                            case 'pending':
                                $status_class = 'pending';
                                break;
                            case 'disetujui':
                                $status_class = 'disetujui';
                                break;
                            case 'ditolak':
                                $status_class = 'ditolak';
                                break;
                            case 'selesai':
                                $status_class = 'selesai';
                                break;
                            default:
                                $status_class = ''; // default jika ada status tidak dikenal
                                break;
                        }
                        echo '<span class="status-badge ' . $status_class . '">' . $status_text . '</span>';
                        ?>
                    </div>
                <?php
                    endwhile;
                else :
                ?>
                    <p>Anda belum memiliki riwayat peminjaman.</p>
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