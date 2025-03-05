<?php
include 'koneksi.php';

// Jika form Sign Up disubmit
if (isset($_POST['sign_up'])) {
    // Mengambil data dari form
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($koneksi, $_POST['confirm_password']);
    $id_divisi = mysqli_real_escape_string($koneksi, $_POST['id_divisi']); // Pastikan id_divisi diambil dengan benar
    
    // Mengecek apakah password dan confirm password cocok
    if ($password != $confirm_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Hash password untuk keamanan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Menentukan level pengguna berdasarkan divisi
        $level = 1; // Default level untuk user biasa
        if ($id_divisi == 1) { // Divisi IT
            $level = 1; // User di divisi IT
        }
        if ($id_divisi == 1 && $username == 'Admin IT') { // Admin hanya di divisi IT
            $level = 2; // Admin di divisi IT
        }
        if ($id_divisi == 1 && $username == 'IT Manager') { // IT Manager hanya di divisi IT
            $level = 3; // Administrator
        }

        // Memeriksa apakah username sudah ada
        $check_query = "SELECT * FROM tbl_user WHERE nama = ?";
        $stmt_check = mysqli_prepare($koneksi, $check_query);
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            $error = "Username sudah digunakan. Coba yang lain.";
        } else {
            // Menambahkan pengguna baru ke dalam tabel tbl_user
            $query = "INSERT INTO tbl_user (nama, password, id_divisi, id_level) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "ssii", $username, $hashed_password, $id_divisi, $level);

            if (mysqli_stmt_execute($stmt)) {
                // Redirect ke halaman login setelah berhasil daftar
                header("Location: login.php");
                exit();
            } else {
                $error = "Gagal mendaftar! Coba lagi.";
            }
        }

        // Menutup statement
        mysqli_stmt_close($stmt_check);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | PT. Phapros Tbk</title>
    <!-- Menambahkan Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <section class="d-flex justify-content-center align-items-center vh-100">
        <div class="sign-up-container p-5 shadow rounded bg-white" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Sign Up</h2>
            <!-- Form Sign Up -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error; ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <!-- Dropdown untuk memilih Divisi -->
                <div class="form-group">
                    <label for="id_divisi">Divisi</label>
                    <select name="id_divisi" id="id_divisi" class="form-control" required>
                        <?php
                        // Mengambil data divisi dari tabel tbl_divisi
                        $query_divisi = "SELECT * FROM tbl_divisi";
                        $result_divisi = mysqli_query($koneksi, $query_divisi);
                        while ($row = mysqli_fetch_assoc($result_divisi)) {
                            echo "<option value='" . $row['id_divisi'] . "'>" . $row['nama_divisi'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="sign_up" class="btn btn-primary btn-block">Sign Up</button>
            </form>
            <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </section>

    <!-- Menambahkan Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
