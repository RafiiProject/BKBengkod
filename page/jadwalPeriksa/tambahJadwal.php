<?php
include '../../koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form
    $idPoli = $_SESSION['id_poli'];
    $idDokter = $_SESSION['id'];
    $hari = $_POST["hari"];
    $jamMulai = $_POST["jamMulai"];
    $jamSelesai = $_POST["jamSelesai"];

    // Cek apakah dokter ini sudah memiliki jadwal aktif
    $cekAktifQuery = "SELECT * FROM jadwal_periksa WHERE id_dokter = '$idDokter' AND aktif = '1'";
    $resultAktif = mysqli_query($mysqli, $cekAktifQuery);

    if (mysqli_num_rows($resultAktif) > 0) {
        echo '<script>alert("Dokter hanya dapat memiliki satu jadwal aktif! Nonaktifkan jadwal lain terlebih dahulu.");window.location.href="../../jadwalPeriksa.php";</script>';
    } else {
        // Validasi jadwal overlap
        $queryOverlap = "SELECT * FROM jadwal_periksa 
                         WHERE id_dokter = '$idDokter' 
                         AND hari = '$hari' 
                         AND ((jam_mulai < '$jamSelesai' AND jam_selesai > '$jamMulai') 
                         OR (jam_mulai < '$jamMulai' AND jam_selesai > '$jamMulai'))";

        $resultOverlap = mysqli_query($mysqli, $queryOverlap);
        
        if (mysqli_num_rows($resultOverlap) > 0) {
            echo '<script>alert("Jadwal ini berbenturan dengan jadwal lain.");window.location.href="../../jadwalPeriksa.php";</script>';
        } else {
            // Tambahkan jadwal baru
            $query = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, aktif) 
                      VALUES ('$idDokter', '$hari', '$jamMulai', '$jamSelesai', '2')";

            if (mysqli_query($mysqli, $query)) {
                echo '<script>';
                echo 'alert("Jadwal berhasil ditambahkan!");';
                echo 'window.location.href = "../../jadwalPeriksa.php";';
                echo '</script>';
                exit();
            } else {
                echo "Error: " . $query . "<br>" . mysqli_error($mysqli);
            }
        }
    }
}

// Tutup koneksi
mysqli_close($mysqli);
?>
