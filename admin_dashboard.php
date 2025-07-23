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

// Memeriksa tingkat akses pengguna. Dashboard ini untuk user_level 2 (Admin IT/Peminjaman).
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 2) {
    echo "<script>alert('Role user tidak sesuai! Anda tidak memiliki akses ke halaman ini.'); window.location.href='login.php';</script>";
    exit();
}

// Ambil username dari session untuk tampilan
$username = $_SESSION['username'];

// Query untuk mengambil semua data peminjaman beserta detail barang dan nama pengguna
// PENTING: Untuk produksi, gunakan Prepared Statements untuk keamanan SQL Injection!
$query_peminjaman = "SELECT 
                        p.id_pinjam, 
                        b.Merk_tipe, 
                        b.prosessor, 
                        b.ram, 
                        b.storage, 
                        b.jenis_storage,
                        p.keperluan_pinjam, 
                        p.tgl_pinjam, 
                        p.tgl_kembali, 
                        u1.nama AS nama_peminjam,
                        d.nama_divisi AS nama_divisi_peminjam, 
                        COALESCE(u2.nama, '-') AS nama_pic_it, 
                        p.status
                    FROM tbl_peminjaman p
                    JOIN tbl_barang b ON p.id_barang = b.id_barang
                    JOIN tbl_user u1 ON p.pic_pinjam = u1.id_user
                    JOIN tbl_divisi d ON u1.id_divisi = d.id_divisi
                    LEFT JOIN tbl_user u2 ON p.pic_it = u2.id_user
                    ORDER BY p.tgl_pinjam DESC";

$result_peminjaman = mysqli_query($koneksi, $query_peminjaman);

// Cek jika query gagal
if (!$result_peminjaman) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Admin Dashboard | PT. Phapros Tbk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" type="x-icon" href="assets/img/icon-removebg-preview.png">
    <style>
        /* CSS Kustom Tambahan untuk Tampilan yang Lebih Keren */
        body {
            background-color: #f8f9fa; /* Warna latar belakang ringan */
        }
        .navbar {
            background-color: #003366; /* Warna biru tua khas PT. Phapros (contoh) */
            padding: 1rem 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: #fff !important;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
            margin-right: 1.5rem;
            font-weight: 500;
        }
        .navbar-nav .nav-link:hover {
            color: #a0d9ff !important; /* Warna hover yang lebih terang */
        }
        .profile-info {
            color: #fff;
            margin-right: 1rem;
        }
        .btn-logout {
            background-color: #dc3545; /* Warna merah untuk logout */
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.3rem;
            text-decoration: none;
        }
        .btn-logout:hover {
            background-color: #c82333;
            color: #fff;
        }

        .main-content {
            padding-top: 100px; /* Sesuaikan dengan tinggi navbar */
            min-height: calc(100vh - 100px); /* Pastikan konten tidak terpotong footer */
        }

        .section-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 0.5rem;
        }
        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
        }

        .data-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            overflow: hidden; /* Agar shadow tidak terpotong */
            transition: transform 0.2s ease-in-out;
        }
        .data-card:hover {
            transform: translateY(-5px); /* Efek hover mengangkat kartu */
        }
        .data-card-header {
            background-color: #e9ecef;
            padding: 1rem 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
        }
        .data-card-body {
            padding: 1.5rem;
        }
        .data-card-body p {
            margin-bottom: 0.5rem;
            color: #495057;
        }
        .data-card-body small {
            color: #6c757d;
            display: block;
            margin-top: 0.3rem;
        }
        .badge {
            font-size: 0.85em;
            padding: 0.5em 0.8em;
            border-radius: 0.3rem;
            font-weight: 600;
        }
        .action-buttons-card {
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
            text-align: right;
        }
        .action-buttons-card .btn {
            margin-left: 0.5rem;
        }

        /* Footer */
        .footer {
            background-color: #343a40; /* Warna gelap untuk footer */
            color: #fff;
            padding: 2rem 0;
            text-align: center;
            margin-top: 3rem; /* Jarak dari konten atas */
        }
        .footer__logo {
            font-weight: bold;
            font-size: 1.3rem;
            color: #a0d9ff;
            text-decoration: none;
        }
        .footer__description {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #adb5bd;
        }
        .footer__social a {
            color: #fff;
            font-size: 1.5rem;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }
        .footer__social a:hover {
            color: #a0d9ff;
        }
        .footer__copy {
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PT. Phapros Tbk</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#peminjaman-list"><i class='bx bx-list-ul'></i> Daftar Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about"><i class='bx bx-info-circle'></i> Tentang Sistem</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="profile-info me-3">Halo, <?php echo htmlspecialchars($username); ?> (IT Admin)</span>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <section class="py-5 text-center bg-light rounded-3 shadow-sm mb-5">
                <h1 class="display-5 fw-bold text-primary">Dashboard IT Admin</h1>
                <p class="fs-4 text-muted">Kelola semua permintaan peminjaman laptop dengan mudah.</p>
                <a href="#peminjaman-list" class="btn btn-primary btn-lg mt-3">Lihat Daftar Peminjaman</a>
            </section>

            <section id="peminjaman-list" class="mb-5">
                <div class="section-header">
                    <span class="section-subtitle">Data Permintaan</span>
                    <h2 class="section-title">Manajemen Peminjaman Laptop</h2>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php if (mysqli_num_rows($result_peminjaman) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($result_peminjaman)) : ?>
                            <div class="col">
                                <div class="data-card">
                                    <div class="data-card-header">
                                        Peminjaman #<?= htmlspecialchars($row['id_pinjam']); ?>
                                        <span class="float-end">
                                            <?php
                                            $status_class = '';
                                            $status_text = ucfirst(htmlspecialchars($row['status']));
                                            switch ($row['status']) {
                                                case 'pending':
                                                    $status_class = 'bg-warning text-dark';
                                                    break;
                                                case 'disetujui':
                                                    $status_class = 'bg-success';
                                                    break;
                                                case 'ditolak':
                                                    $status_class = 'bg-danger';
                                                    break;
                                                case 'selesai':
                                                    $status_class = 'bg-secondary';
                                                    break;
                                                default:
                                                    $status_class = 'bg-info';
                                                    break;
                                            }
                                            echo '<span class="badge ' . $status_class . '">' . $status_text . '</span>';
                                            ?>
                                        </span>
                                    </div>
                                    <div class="data-card-body">
                                        <p><strong>Laptop:</strong> <?= htmlspecialchars($row['Merk_tipe']); ?></p>
                                        <small>Processor: <?= htmlspecialchars($row['prosessor']); ?> | RAM: <?= htmlspecialchars($row['ram']); ?>GB | Storage: <?= htmlspecialchars($row['storage']); ?>GB (<?= htmlspecialchars($row['jenis_storage']); ?>)</small>
                                        <p class="mt-3"><strong>Keperluan:</strong> <?= htmlspecialchars($row['keperluan_pinjam']); ?></p>
                                        <p><strong>Tanggal Pinjam:</strong> <?= htmlspecialchars($row['tgl_pinjam']); ?></p>
                                        <p><strong>Tanggal Kembali:</strong> <?= htmlspecialchars($row['tgl_kembali']); ?></p>
                                        <p><strong>PIC Peminjam:</strong> <?= htmlspecialchars($row['nama_peminjam']); ?> | </strong> <?= htmlspecialchars($row['nama_divisi_peminjam']); ?></p> 
                                        <p><strong>PIC IT (Penanganan):</strong> <?= htmlspecialchars($row['nama_pic_it']); ?></p>
                                    </div>
                                    <div class="action-buttons-card">
                                        <?php if ($row['status'] == "pending") : ?>
                                            <form action="proses_persetujuan.php" method="POST" class="d-inline">
                                                <input type="hidden" name="id_pinjam" value="<?= htmlspecialchars($row['id_pinjam']); ?>">
                                                <input type="hidden" name="pic_it" value="<?= htmlspecialchars($_SESSION['user_id']); ?>">
                                                <button type="submit" name="approve" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman ini?')">Setujui</button>
                                                <button type="submit" name="reject" class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman ini?')">Tolak</button>
                                            </form>
                                        <?php elseif ($row['status'] == "disetujui") : ?>
                                            <a href="proses_pengembalian.php?id_pinjam=<?= htmlspecialchars($row['id_pinjam']); ?>" class="btn btn-info btn-sm" onclick="return confirm('Apakah peminjaman ini sudah selesai dan laptop sudah dikembalikan?')">Tandai Selesai</a>
                                        <?php else: ?>
                                            <span class="text-muted">Aksi tidak tersedia</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div class="col-12 text-center py-4">
                            <p class="lead">Tidak ada permintaan peminjaman saat ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section id="about" class="py-5 text-center">
                <div class="section-header">
                    <span class="section-subtitle">Informasi</span>
                    <h2 class="section-title">Tentang Sistem Ini</h2>
                </div>
                <p class="lead text-muted">Sistem manajemen peminjaman laptop PT. Phapros Tbk dirancang untuk mempermudah proses peminjaman dan pengelolaan aset laptop perusahaan secara efisien.</p>
                <p class="text-muted">Jika ada pertanyaan atau memerlukan bantuan teknis, silakan hubungi tim IT.</p>
            </section>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <a href="#" class="footer__logo">PT. Phapros Tbk</a>
                    <span class="footer__description">Sistem Manajemen Laptop</span>
                    <div class="mt-3">
                        <a href="#" class="footer__social"><i class='bx bxl-facebook'></i></a>
                        <a href="#" class="footer__social"><i class='bx bxl-instagram'></i></a>
                        <a href="#" class="footer__social"><i class='bx bxl-whatsapp'></i></a>
                    </div>
                </div>
            </div>
            <p class="footer__copy mt-4">&#169; 2025 PT. Phapros Tbk. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>