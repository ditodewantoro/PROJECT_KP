<?php
// Memulai sesi PHP untuk mengelola status login pengguna
session_start();

// Menggabungkan file koneksi database
include 'koneksi.php';

// Memeriksa apakah pengguna sudah login (variabel sesi 'username' harus ada)
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login.php
    header('Location: login.php');
    exit(); // Hentikan eksekusi skrip lebih lanjut
}

// Memeriksa tingkat akses pengguna. Dashboard ini hanya untuk user_level 3 (administrator).
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 3) {
    // Jika tingkat akses tidak sesuai, tampilkan pesan alert dan arahkan kembali ke login.php
    echo "<script>alert('Role user tidak sesuai! Anda tidak memiliki akses admin.'); window.location.href='login.php';</script>";
    exit(); // Hentikan eksekusi skrip
}

// Mengambil data laptop dari database untuk ditampilkan di tabel
$laptop_result = mysqli_query($koneksi, "SELECT * FROM tbl_barang ORDER BY Merk_tipe ASC");

// Mengambil data peminjaman dari database, termasuk informasi user
// Bergabung dengan tbl_barang untuk detail laptop dan tbl_user untuk nama peminjam dan PIC IT
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
                        u1.nama AS nama_peminjam,
                        COALESCE(u2.nama, '-') AS nama_pic_it,
                        p.status
                    FROM tbl_peminjaman p
                    JOIN tbl_barang b ON p.id_barang = b.id_barang
                    JOIN tbl_user u1 ON p.pic_pinjam = u1.id_user
                    LEFT JOIN tbl_user u2 ON p.pic_it = u2.id_user
                    ORDER BY p.tgl_pinjam DESC";
$peminjaman_result = mysqli_query($koneksi, $peminjaman_query);

// Ambil username dari session untuk tampilan
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard | PT. Phapros Tbk</title>
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

        /* Styling untuk tabel Bootstrap */
        .table-responsive {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            padding: 20px;
        }
        .table-responsive table {
            margin-bottom: 0; /* Hapus margin default tabel */
        }
        .table-responsive th, .table-responsive td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.85em;
            padding: 0.5em 0.8em;
            border-radius: 0.3rem;
            font-weight: 600;
        }
        .action-buttons-table .btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem; /* Tambah margin-bottom agar tombol tidak terlalu rapat di mobile */
        }
        .action-buttons-table form {
            display: inline-block; /* Agar form dan tombol lain bisa sejajar */
            margin-right: 0.5rem;
        }

        /* Styling untuk tombol di bawah tabel (sesuaikan dengan Bootstrap) */
        .button-below-table {
            margin-top: 20px;
            text-align: right;
            width: 100%;
            clear: both;
            display: block;
        }
        .button-below-table .btn { /* Menggunakan .btn dari Bootstrap */
            display: inline-block;
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
                        <a class="nav-link" href="#laptop-management"><i class='bx bx-laptop'></i> Manajemen Laptop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#peminjaman-management"><i class='bx bx-list-ul'></i> Manajemen Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about"><i class='bx bx-info-circle'></i> Tentang Sistem</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="profile-info me-3">Halo, <?php echo htmlspecialchars($username); ?></span>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <section class="py-5 text-center bg-light rounded-3 shadow-sm mb-5">
                <h1 class="display-5 fw-bold text-primary">Dashboard Administrator Utama</h1>
                <p class="fs-4 text-muted">Kelola inventaris laptop dan semua permintaan peminjaman.</p>
                <a href="#laptop-management" class="btn btn-primary btn-lg mt-3">Mulai Mengelola</a>
            </section>

            <section id="laptop-management" class="mb-5">
                <div class="section-header">
                    <span class="section-subtitle">Inventaris</span>
                    <h2 class="section-title">Manajemen Laptop</h2>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr class="table-dark">
                                <th>No</th>
                                <th>Merk/Tipe</th>
                                <th>Spesifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no_laptop = 1;
                            if (mysqli_num_rows($laptop_result) > 0) :
                                while ($row_laptop = mysqli_fetch_assoc($laptop_result)) :
                            ?>
                                <tr>
                                    <td><?= $no_laptop++; ?></td>
                                    <td><?= htmlspecialchars($row_laptop['Merk_tipe']); ?></td>
                                    <td>
                                        <?= htmlspecialchars($row_laptop['prosessor']); ?> |
                                        RAM: <?= htmlspecialchars($row_laptop['ram']); ?>GB |
                                        Storage: <?= htmlspecialchars($row_laptop['storage']); ?>GB (<?= htmlspecialchars($row_laptop['jenis_storage']); ?>)
                                    </td>
                                    <td class="action-buttons-table">
                                        <a href="edit.php?id=<?= htmlspecialchars($row_laptop['id_barang']); ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="hapus.php?id_barang=<?= htmlspecialchars($row_laptop['id_barang']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus laptop ini? Data peminjaman terkait juga akan terpengaruh.')">Hapus</a>
                                    </td>
                                </tr>
                            <?php
                                endwhile;
                            else :
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center py-3">Belum ada data laptop.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="button-below-table">
                    <a href="tambah.php" class="btn btn-success">+ Tambah Laptop Baru</a>
                </div>
            </section>

            <section id="peminjaman-management" class="mb-5">
                <div class="section-header">
                    <span class="section-subtitle">Data Permintaan</span>
                    <h2 class="section-title">Manajemen Peminjaman</h2>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr class="table-dark">
                                <th>No</th>
                                <th>Barang</th>
                                <th>Keperluan</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Peminjam</th>
                                <th>PIC IT</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no_peminjaman = 1;
                            if (mysqli_num_rows($peminjaman_result) > 0) :
                                while ($row_peminjaman = mysqli_fetch_assoc($peminjaman_result)) :
                            ?>
                                <tr>
                                    <td><?= $no_peminjaman++; ?></td>
                                    <td>
                                        <?= htmlspecialchars($row_peminjaman['Merk_tipe']); ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($row_peminjaman['prosessor']); ?> | RAM: <?= htmlspecialchars($row_peminjaman['ram']); ?>GB</small>
                                    </td>
                                    <td><?= htmlspecialchars($row_peminjaman['keperluan_pinjam']); ?></td>
                                    <td><?= htmlspecialchars($row_peminjaman['tgl_pinjam']); ?></td>
                                    <td><?= htmlspecialchars($row_peminjaman['tgl_kembali']); ?></td>
                                    <td><?= htmlspecialchars($row_peminjaman['nama_peminjam']); ?></td>
                                    <td><?= htmlspecialchars($row_peminjaman['nama_pic_it']); ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = ucfirst(htmlspecialchars($row_peminjaman['status']));
                                        switch ($row_peminjaman['status']) {
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
                                    </td>
                                    <td class="action-buttons-table">
                                        <?php if ($row_peminjaman['status'] == "pending") : ?>
                                            <form action="proses_persetujuan_admin.php" method="POST" class="d-inline">
                                                <input type="hidden" name="id_pinjam" value="<?= htmlspecialchars($row_peminjaman['id_pinjam']); ?>">
                                                <input type="hidden" name="pic_it" value="<?= htmlspecialchars($_SESSION['user_id']); ?>">
                                                <button type="submit" name="approve" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman ini?')">Setujui</button>
                                                <button type="submit" name="reject" class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman ini?')">Tolak</button>
                                            </form>
                                        <?php elseif ($row_peminjaman['status'] == "disetujui") : ?>
                                            <a href="proses_pengembalian.php?id_pinjam=<?= htmlspecialchars($row_peminjaman['id_pinjam']); ?>" class="btn btn-info btn-sm" onclick="return confirm('Apakah peminjaman ini sudah selesai dan laptop sudah dikembalikan?')">Selesai</a>
                                        <?php else: ?>
                                            <a href="hapus_peminjaman.php?id_pinjam=<?= htmlspecialchars($row_peminjaman['id_pinjam']); ?>" class="btn btn-warning btn-sm" onclick="return confirm('PERINGATAN! Ini akan menghapus permanen riwayat peminjaman ini. Lanjutkan?')">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                                endwhile;
                            else :
                            ?>
                                <tr>
                                    <td colspan="9" class="text-center py-3">Tidak ada data peminjaman.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                    <span class="footer__description">Laptop Management</span>
                    <div class="mt-3">
                        <a href="#" class="footer__social"><i class='bx bxl-facebook'></i></a>
                        <a href="#" class="footer__social"><i class='bx bxl-instagram'></i></a>
                        <a href="#" class="bx bxl-whatsapp'></i></a>
                    </div>
                </div>
            </div>
            <p class="footer__copy mt-4">&#169; 2025 PT. Phapros Tbk. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>