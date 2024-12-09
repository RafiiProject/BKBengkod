<?php

require '../../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil tahun dan bulan saat ini
    $tahun = date('Y');   // Tahun penuh (4 digit)
    $bulan = date('m');   // Bulan (2 digit)

    // Ambil data dari form
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $password = md5($nama); // Hash password (gunakan hash lebih aman seperti password_hash)

    // Validasi input tidak kosong
    if (empty($nama) || empty($no_ktp) || empty($alamat) || empty($no_hp)) {
        echo '<script>alert("Semua kolom harus diisi!");window.location.href="../../pasien.php";</script>';
        exit();
    }

    // Cek apakah no_ktp sudah ada
    $cekNoKTP = "SELECT * FROM pasien WHERE no_ktp = '$no_ktp'";
    $queryCekKTP = mysqli_query($mysqli, $cekNoKTP);
    if (mysqli_num_rows($queryCekKTP) > 0) {
        echo '<script>alert("No KTP telah terdaftar sebelumnya");window.location.href="../../pasien.php";</script>';
        exit();
    }

    // Buat nomor rekam medis dengan format yyyymm-xxx
    $prefix = sprintf('%s%s', $tahun, $bulan); // Format prefix yyyymm
    $getLastData = "SELECT no_rm FROM pasien WHERE no_rm LIKE '$prefix-%' ORDER BY no_rm DESC LIMIT 1";
    $queryGetLast = mysqli_query($mysqli, $getLastData);

    // Periksa apakah ada data dengan prefix ini
    if (mysqli_num_rows($queryGetLast) > 0) {
        $lastData = mysqli_fetch_assoc($queryGetLast);
        // Ambil bagian setelah yyyymm- untuk menentukan nomor urut terakhir
        $substring = substr($lastData['no_rm'], 7); // Mengambil 3 digit terakhir setelah "yyyymm-"
        $urutanTerakhir = (int)$substring + 1; // Increment nomor urut
    } else {
        $urutanTerakhir = 1; // Jika tidak ada data, mulai dari 1
    }

    // Format nomor RM menjadi 3 digit
    $no_rm = sprintf('%s-%03d', $prefix, $urutanTerakhir);

    // Insert data ke database
    $insertData = "INSERT INTO pasien (nama, password, alamat, no_ktp, no_hp, no_rm) VALUES ('$nama', '$password', '$alamat', '$no_ktp', '$no_hp', '$no_rm')";
    $queryInsert = mysqli_query($mysqli, $insertData);

    if ($queryInsert) {
        echo '<script>alert("Pendaftaran akun berhasil");window.location.href="../../pasien.php";</script>';
    } else {
        echo '<script>alert("Pendaftaran akun gagal: ' . mysqli_error($mysqli) . '");window.location.href="../../pasien.php";</script>';
    }
}

// Tutup koneksi
mysqli_close($mysqli);
?>
