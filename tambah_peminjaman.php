<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Peminjaman Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="section-title">Tambah Peminjaman Barang</h2>
        <form method="POST" action="proses_peminjaman.php">
            <div class="mb-3 form-group">
                <label for="id_barang">Laptop:</label>
                <select name="id_barang" class="form-control" required>
                    <?php
                    include 'koneksi.php';
                    $result = mysqli_query($koneksi, "SELECT * FROM tbl_barang");
                    while ($row = mysqli_fetch_assoc($result)) :
                    ?>
                        <option value="<?= $row['id_barang']; ?>"><?= $row['Merk_tipe']; ?> - <?= $row['prosessor']; ?> | RAM: <?= $row['ram']; ?>GB | Storage: <?= $row['storage']; ?>GB</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3 form-group">
                <label for="keperluan_pinjam">Keperluan Peminjaman:</label>
                <textarea name="keperluan_pinjam" class="form-control" required></textarea>
            </div>
            <div class="mb-3 form-group">
                <label for="tgl_pinjam">Tanggal Peminjaman:</label>
                <input type="date" name="tgl_pinjam" class="form-control" required>
            </div>
            <div class="mb-3 form-group">
                <label for="tgl_kembali">Tanggal Kembali:</label>
                <input type="date" name="tgl_kembali" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button> 
            <button type="button" class="btn btn-secondary">
                <a href="user_dashboard.php" style="color:white;">Kembali</a>
            </button>
        </form>
    </div>
</body>
</html>
