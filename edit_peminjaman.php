<?php
include 'koneksi.php';

if (isset($_GET['id_pinjam'])) {
    $id_pinjam = $_GET['id_pinjam'];

    // Ambil data peminjaman berdasarkan id_pinjam
    $query = "SELECT * FROM tbl_peminjaman WHERE id_pinjam = $id_pinjam";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);

    // Jika data tidak ditemukan
    if (!$data) {
        echo "Data tidak ditemukan!";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Peminjaman Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Tambahkan stylesheet Anda -->
</head>
<body>
    <div class="container mt-4">
        <h2 class="section-title">Edit Peminjaman Barang</h2>
        <form method="POST" action="proses_edit_peminjaman.php">
            <input type="hidden" name="id_pinjam" value="<?= $data['id_pinjam']; ?>">

            <div class="mb-3 form-group">
                <label for="id_barang">Laptop:</label>
                <select name="id_barang" class="form-control" required>
                    <?php
                    $barang_query = mysqli_query($koneksi, "SELECT * FROM tbl_barang");
                    while ($barang = mysqli_fetch_assoc($barang_query)) :
                    ?>
                        <option value="<?= $barang['id_barang']; ?>" <?= ($data['id_barang'] == $barang['id_barang']) ? 'selected' : ''; ?>>
                            <?= $barang['Merk_tipe']; ?> - <?= $barang['prosessor']; ?> | RAM: <?= $barang['ram']; ?>GB | Storage: <?= $barang['storage']; ?>GB
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3 form-group">
                <label for="keperluan_pinjam">Keperluan Peminjaman:</label>
                <textarea name="keperluan_pinjam" class="form-control" required><?= $data['keperluan_pinjam']; ?></textarea>
            </div>
            <div class="mb-3 form-group">
                <label for="tgl_pinjam">Tanggal Peminjaman:</label>
                <input type="date" name="tgl_pinjam" class="form-control" value="<?= $data['tgl_pinjam']; ?>" required>
            </div>
            <div class="mb-3 form-group">
                <label for="tgl_kembali">Tanggal Kembali:</label>
                <input type="date" name="tgl_kembali" class="form-control" value="<?= $data['tgl_kembali']; ?>" required>
            </div>
            <div class="mb-3 form-group">
                <label for="pic_it">PIC IT:</label>
                <input type="text" name="pic_it" class="form-control" value="<?= $data['pic_it']; ?>" required>
            </div>
            <div class="mb-3 form-group">
                <label for="pic_pinjam">PIC Peminjaman:</label>
                <input type="text" name="pic_pinjam" class="form-control" value="<?= $data['pic_pinjam']; ?>" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
