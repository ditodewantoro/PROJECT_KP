<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 2) {
    echo "<script>alert('Role user tidak sesuai!'); window.location.href='login.php';</script>";
    exit();
}
// Ambil data peminjaman dari database
$query = "SELECT p.id_pinjam, b.Merk_tipe, b.prosessor, b.ram, b.storage, p.keperluan_pinjam, p.tgl_pinjam, p.tgl_kembali, p.pic_pinjam, u1.nama AS nama_pinjam, p.pic_it, COALESCE(u2.nama, '-') AS nama_it, p.status
          FROM tbl_peminjaman p
          JOIN tbl_barang b ON p.id_barang = b.id_barang
          JOIN tbl_user u1 ON p.pic_pinjam = u1.id_user
          LEFT JOIN tbl_user u2 ON p.pic_it = u2.id_user";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin - Manajemen Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="section-title">Dashboard Admin - Manajemen Peminjaman</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Keperluan</th>
                    <th>Tgl Peminjaman</th>
                    <th>Tgl Kembali</th>
                    <th>PIC Peminjam</th>
                    <th>PIC IT</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) :
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['Merk_tipe'] . " - " . $row['prosessor'] . " | RAM: " . $row['ram'] . "GB | Storage: " . $row['storage'] . "GB"; ?></td>
                        <td><?= $row['keperluan_pinjam']; ?></td>
                        <td><?= $row['tgl_pinjam']; ?></td>
                        <td><?= $row['tgl_kembali']; ?></td>
                        <td><?= $row['nama_pinjam']; ?></td>
                        <td><?= $row['nama_it']; ?></td>
                        <td>
                            <?php
                            if ($row['status'] == "pending") {
                                echo '<span class="badge bg-warning">Pending</span>';
                            } elseif ($row['status'] == "disetujui") {
                                echo '<span class="badge bg-success">Disetujui</span>';
                            } elseif ($row['status'] == "ditolak") {
                                echo '<span class="badge bg-danger">Ditolak</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == "pending") : ?>
                                <form action="proses_persetujuan.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id_pinjam" value="<?= $row['id_pinjam']; ?>">
                                    <input type="hidden" name="pic_it" value="<?= $_SESSION['user_id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Setujui</button>
                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Tolak</button>
                                </form>
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
