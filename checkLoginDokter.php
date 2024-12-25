<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah username ada di database
    $query = "SELECT * FROM dokter WHERE nama = ?";
    $stmt = mysqli_prepare($mysqli, $query);

    if (!$stmt) {
        die("Query gagal dipersiapkan: " . mysqli_error($mysqli));
    }

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($data = mysqli_fetch_assoc($result)) {
        // Verifikasi password menggunakan password_verify
        if (password_verify($password, $data['password'])) {
            // Jika login berhasil, simpan data ke sesi
            $_SESSION['id'] = $data['id'];
            $_SESSION['username'] = $data['nama'];
            $_SESSION['password'] = $data['password'];
            $_SESSION['akses'] = "dokter";

            header("location: dashboard_dokter.php"); // Redirect ke dashboard dokter
            exit();
        } else {
            echo '<script>alert("Password salah!");location.href="loginDokter.php";</script>';
        }
    } else {
        echo '<script>alert("Username tidak ditemukan!");location.href="loginDokter.php";</script>';
    }

    mysqli_stmt_close($stmt);
}
?>
