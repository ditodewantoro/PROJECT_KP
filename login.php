<?php
session_start(); // Memulai sesi

include 'koneksi.php';

// Jika form login disubmit
if (isset($_POST['login'])) {
    // Mengambil data dari form login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mengambil data user menggunakan prepared statement
    $query = "SELECT * FROM tbl_user WHERE nama = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Cek apakah data ditemukan
    if ($user = mysqli_fetch_assoc($result)) {
        // Verifikasi password
        if ($password === $user['password']) {
            // Simpan data user ke sesi
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['nama'];
            $_SESSION['user_level'] = $user['id_level'];
            $_SESSION['user_divisi'] = $user['id_divisi'];

            // Arahkan pengguna ke halaman sesuai level
            switch ($user['id_level']) {
                case 1:
                    header("Location: user_dashboard.php");
                    break;
                case 2:
                    header("Location: admin_dashboard.php");
                    break;
                case 3:
                    header("Location: admin_panel.php");
                    break;
                default:
                    echo "Level pengguna tidak dikenali.";
                    exit();
            }
            exit();
        } else {
            echo "<script>alert('Username atau password salah!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Username atau password salah!'); window.location.href='login.php';</script>";
    }
    // Tutup statement
    mysqli_stmt_close($stmt);
}
// Tutup koneksi
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PT. Phapros Tbk</title>
    <!-- Menambahkan Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <section class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-container p-5 shadow rounded bg-white">
            <h2 class="text-center mb-4">Login</h2>
            <!-- Form login -->
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="text-center mt-3 text-black">Belum punya akun? <a href="sign_up.php">Daftar di sini</a></p>
        </div>
    </section>

    <!-- Menambahkan Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
