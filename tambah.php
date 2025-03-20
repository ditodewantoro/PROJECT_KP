<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barang = $_POST['id_barang']; // Ambil input ID secara manual
    $Merk_tipe = $_POST['Merk_tipe'];
    $prosessor = $_POST['prosessor'];
    $ram = $_POST['ram'];
    $storage = $_POST['storage'];
    $jenis_storage = $_POST['jenis_storage'];
    
    $check = mysqli_query($koneksi, "SELECT * FROM tbl_barang WHERE id_barang = '$id_barang'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Error: ID Barang sudah ada!'); window.history.back();</script>";
        exit;
    }

    $query = "INSERT INTO tbl_barang (id_barang, Merk_tipe, prosessor, ram, storage, jenis_storage) 
              VALUES ('$id_barang', '$Merk_tipe', '$prosessor', '$ram', '$storage', '$jenis_storage')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: admin_panel.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="section-title">Tambah Laptop</h2>
        <form method="POST">
            <div class="mb-3">
                <label>ID Barang</label>
                <input type="number" name="id_barang" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Merk & Tipe</label>
                <input type="text" name="Merk_tipe" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Prosesor</label>
                <input type="text" name="prosessor" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>RAM (GB)</label>
                <input type="number" name="ram" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Storage (GB)</label>
                <input type="number" name="storage" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Jenis Storage</label>
                <select name="jenis_storage" class="form-control" required>
                    <option value="SSD">SSD</option>
                    <option value="HDD">HDD</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button> 
            <button type="button" class="btn btn-secondary">
                <a href="admin_panel.php" style="color:white;">Kembali</a>
            </button>
        </form>
    </div>
</body>
</html>
