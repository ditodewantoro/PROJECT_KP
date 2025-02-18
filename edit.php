<?php
include 'koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM tbl_barang WHERE id_barang = '$id'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang = $_POST['id_barang'];
    $Merk_tipe = $_POST['Merk_tipe'];
    $prosessor = $_POST['prosessor'];
    $ram = $_POST['ram'];
    $storage = $_POST['storage'];
    $jenis_storage = $_POST['jenis_storage'];

    $query = "UPDATE tbl_barang SET id_barang='$id_barang', Merk_tipe='$Merk_tipe', prosessor='$prosessor', ram='$ram', storage='$storage', jenis_storage='$jenis_storage' WHERE id_barang='$id'";
    mysqli_query($koneksi, $query);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="section-title">Edit Laptop</h2>
        <form method="POST">
        <div class="mb-3">
                <label>ID Barang</label>
                <input type="text" name="id_barang" class="form-control" value="<?= $data['id_barang']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Merk & Tipe</label>
                <input type="text" name="Merk_tipe" class="form-control" value="<?= $data['Merk_tipe']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Prosesor</label>
                <input type="text" name="prosessor" class="form-control" value="<?= $data['prosessor']; ?>" required>
            </div>
            <div class="mb-3">
                <label>RAM (GB)</label>
                <input type="number" name="ram" class="form-control" value="<?= $data['ram']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Storage (GB)</label>
                <input type="number" name="storage" class="form-control" value="<?= $data['storage']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Jenis Storage</label>
                <select name="jenis_storage" class="form-control" required>
                    <option value="SSD" <?= ($data['jenis_storage'] == "SSD") ? "selected" : ""; ?>>SSD</option>
                    <option value="HDD" <?= ($data['jenis_storage'] == "HDD") ? "selected" : ""; ?>>HDD</option>
                </select>
            </div>
            
        <button type="submit" class="btn btn-success">Simpan Perubahan</button> 
        <button type="button" class="btn btn-secondary">
            <a href="index.php" style="color: white;">Kembali</a>
        </button>   
        </form>
    </div>
</body>
</html>
