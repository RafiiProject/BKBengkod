<?php
include 'koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form
    $idPoli = $_SESSION['id_poli'];
    $idDokter = $_SESSION['id'];
    $hari = $_POST["hari"];
    $jamMulai = $_POST["jamMulai"];
    $jamSelesai = $_POST["jamSelesai"];

    // Query untuk cek jadwal yang bertabrakan
    $queryOverlap = "SELECT * FROM jadwal_periksa 
                     WHERE id_dokter = '$idDokter' AND hari = '$hari' 
                     AND ((jam_mulai < '$jamSelesai' AND jam_selesai > '$jamMulai') 
                     OR (jam_mulai < '$jamMulai' AND jam_selesai > '$jamMulai'))";

    $resultOverlap = mysqli_query($mysqli, $queryOverlap);
    
    if (mysqli_num_rows($resultOverlap) > 0) {
        echo '<script>alert("Dokter lain telah mengambil jadwal ini!");window.location.href="../jadwalPeriksa.php";</script>';
    } else {
        // Query untuk menambahkan jadwal baru
        $queryInsert = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai) 
                        VALUES ('$idDokter', '$hari', '$jamMulai', '$jamSelesai')";

        if (mysqli_query($mysqli, $queryInsert)) {
            echo '<script>';
            echo 'alert("Jadwal berhasil ditambahkan!");';
            echo 'window.location.href = "../jadwalPeriksa.php";';
            echo '</script>';
            exit();
        } else {
            // Jika terjadi kesalahan, tampilkan pesan error
            echo "Error: " . $queryInsert . "<br>" . mysqli_error($mysqli);
        }
    }
}

// Tutup koneksi
mysqli_close($mysqli);
?>
