<?php
// Koneksi ke database
require 'koneksi.php';
session_start();

// Validasi akses
if (!isset($_SESSION['akses']) || $_SESSION['akses'] !== "dokter") {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['id'])) {
    die("Session ID tidak ditemukan!");
}

$idDokter = $_SESSION['id'];

// Ambil data dokter berdasarkan sesi login
$query = "SELECT nama, alamat, no_hp, password FROM dokter WHERE id = ?";
$stmt = mysqli_prepare($mysqli, $query);

if (!$stmt) {
    die("Query gagal dipersiapkan: " . mysqli_error($mysqli));
}

mysqli_stmt_bind_param($stmt, 'i', $idDokter);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nama, $alamat, $no_hp, $password);

if (!mysqli_stmt_fetch($stmt)) {
    die("Data dokter tidak ditemukan!");
}

mysqli_stmt_close($stmt);

// Proses update data dokter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBaru = $_POST['nama'];
    $alamatBaru = $_POST['alamat'];
    $teleponBaru = $_POST['no_hp'];
    $passwordBaru = $_POST['password'];

    // Jika password kosong, gunakan nama sebagai default password
    if (empty($passwordBaru)) {
        $passwordBaru = $namaBaru;
    }

    // Enkripsi password baru
    $passwordHash = password_hash($passwordBaru, PASSWORD_DEFAULT);

    $updateQuery = "UPDATE dokter SET nama = ?, alamat = ?, no_hp = ?, password = ? WHERE id = ?";
    $stmtUpdate = mysqli_prepare($mysqli, $updateQuery);

    if (!$stmtUpdate) {
        die("Query gagal dipersiapkan: " . mysqli_error($mysqli));
    }

    mysqli_stmt_bind_param($stmtUpdate, 'ssssi', $namaBaru, $alamatBaru, $teleponBaru, $passwordHash, $idDokter);

    if (mysqli_stmt_execute($stmtUpdate)) {
        echo '<script>alert("Profil dan password berhasil diperbarui!");</script>';
        header("Refresh:0");
    } else {
        echo '<script>alert("Gagal memperbarui profil atau password!");</script>';
    }

    mysqli_stmt_close($stmtUpdate);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Udinus Poliklinik - Edit Data Dokter</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include('components/navbar.php'); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include('components/sidebar.php'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Edit Data Dokter</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php?page=home">Home</a></li>
                                <li class="breadcrumb-item active">Edit Data Dokter</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Edit Data Dokter</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Dokter</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="<?= htmlspecialchars($nama); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Dokter</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat"
                                        value="<?= htmlspecialchars($alamat); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">Nomor HP Dokter</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp"
                                        value="<?= htmlspecialchars($no_hp); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Biarkan kosong untuk menggunakan default (nama)">
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Sidebar</h5>
                <p>Konten tambahan di sidebar</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/dist/js/adminlte.min.js"></script>
</body>

</html>
