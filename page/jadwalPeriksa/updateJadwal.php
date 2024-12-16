<?php
include 'koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form
    $id = $_POST['id'];
    $idPoli = $_SESSION['id_poli'];
    $idDokter = $_SESSION['id'];
    $hari = $_POST["hari"];
    $jamMulai = $_POST["jamMulai"];
    $jamSelesai = $_POST["jamSelesai"];
    $aktif = $_POST['aktif'];

    // Query untuk cek jadwal yang bertabrakan
    $queryOverlap = "SELECT * FROM jadwal_periksa 
                     WHERE id_dokter = '$idDokter' AND hari = '$hari' 
                     AND ((jam_mulai < '$jamSelesai' AND jam_selesai > '$jamMulai') 
                     OR (jam_mulai < '$jamMulai' AND jam_selesai > '$jamMulai'))";

    $resultOverlap = mysqli_query($mysqli, $queryOverlap);
    
    if (mysqli_num_rows($resultOverlap) > 0) {
        echo '<script>alert("Dokter lain telah mengambil jadwal ini!");window.location.href="../../jadwalPeriksa.php";</script>';
    } else {
        // Hanya satu jadwal yang boleh aktif, atur nilai 'aktif' menjadi 'N' untuk jadwal yang sedang di-edit
        $resetAktifQuery = "UPDATE jadwal_periksa SET aktif='N' WHERE id_dokter='$idDokter'";
        mysqli_query($mysqli, $resetAktifQuery);

        // Query untuk mengupdate jadwal
        $queryUpdate = "UPDATE jadwal_periksa 
                        SET hari = '$hari', jam_mulai = '$jamMulai', jam_selesai = '$jamSelesai', aktif = '$aktif' 
                        WHERE id = '$id'";

        if (mysqli_query($mysqli, $queryUpdate)) {
            echo '<script>';
            echo 'alert("Jadwal berhasil diubah!");';
            echo 'window.location.href = "../../jadwalPeriksa.php";';
            echo '</script>';
            exit();
        } else {
            // Jika terjadi kesalahan, tampilkan pesan error
            echo "Error: " . $queryUpdate . "<br>" . mysqli_error($mysqli);
        }
    }
}

// Tutup koneksi
mysqli_close($mysqli);
?>
