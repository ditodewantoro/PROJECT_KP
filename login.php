<?php
include 'koneksi.php';

// Jika form login disubmit
if (isset($_POST['login'])) {
    // Mengambil data dari form login dan sanitasi input
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Query untuk mengecek user yang masuk menggunakan prepared statement
    $query = "SELECT * FROM tbl_user WHERE nama = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Cek apakah data ditemukan
    if (mysqli_num_rows($result) > 0) {
        // Ambil data user
        $user = mysqli_fetch_assoc($result);
        $hashed_password = $user['password']; // Ambil password yang di-hash

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Verifikasi level pengguna dan arahkan ke halaman sesuai
            $user_level = $user['id_level'];
            $user_divisi = $user['id_divisi'];

            // Tentukan akses berdasarkan level
            if ($user_level == 1) {
                // Akses untuk user biasa
                header("Location: user_dashboard.php?divisi=$user_divisi");
                exit();
            } elseif ($user_level == 2) {
                // Akses untuk admin (admin divisi IT)
                header("Location: admin_dashboard.php?divisi=$user_divisi");
                exit();
            } elseif ($user_level == 3) {
                // Akses untuk administrator (IT Manager)
                header("Location: admin_panel.php?divisi=$user_divisi");
                exit();
            } else {
                // Jika level tidak dikenali
                echo "Level pengguna tidak dikenali.";
            }
        } else {
            // Password tidak cocok
            echo "Username atau password salah.";
        }
    } else {
        echo "Username atau password salah.";
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
            <p class="text-center mt-3">Belum punya akun? <a href="sign_up.php">Daftar di sini</a></p>
        </div>
    </section>

    <!-- Menambahkan Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
