<?php
require '../../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idDaftarPoli = $_POST['id'];
    $tgl_Periksa = $_POST['tanggal_periksa'];
    $catatan = $_POST['catatan'];
    $arrayObat = isset($_POST['obat']) ? $_POST['obat'] : []; // Pastikan arrayObat selalu terdefinisi

    // Update data periksa
    $queryUpdate = mysqli_query($mysqli, "UPDATE periksa SET tgl_periksa = '$tgl_Periksa', catatan = '$catatan' WHERE id_daftar_poli = '$idDaftarPoli'");

    if ($queryUpdate) {
        // Hapus semua detail_periksa terkait sebelumnya
        $deleteDetails = mysqli_query($mysqli, "DELETE FROM detail_periksa WHERE id_periksa = (SELECT id FROM periksa WHERE id_daftar_poli = '$idDaftarPoli')");

        if ($deleteDetails) {
            // Ambil id_periksa untuk digunakan pada detail_periksa
            $getIdPeriksaQuery = mysqli_query($mysqli, "SELECT id FROM periksa WHERE id_daftar_poli = '$idDaftarPoli'");
            $getIdPeriksa = mysqli_fetch_assoc($getIdPeriksaQuery);
            $idPeriksa = $getIdPeriksa['id'];

            // Inisialisasi total biaya
            $totalBiaya = 150000; // Harga dasar pemeriksaan

            foreach ($arrayObat as $obat) {
                // Tambahkan obat ke detail_periksa
                $insertDetail = mysqli_query($mysqli, "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES ('$idPeriksa', '$obat')");

                // Ambil harga obat dari database
                $getHargaObat = mysqli_query($mysqli, "SELECT harga FROM obat WHERE id = '$obat'");
                $hargaObat = mysqli_fetch_assoc($getHargaObat)['harga'];

                // Tambahkan harga obat ke total biaya
                $totalBiaya += $hargaObat;

                if (!$insertDetail) {
                    echo '<script>alert("Error dalam menambahkan detail periksa");window.location.href="../../periksaPasien.php"</script>';
                    exit; // Keluar dari skrip jika terjadi kesalahan
                }
            }

            // Update total biaya pada tabel periksa
            $updateTotalBiaya = mysqli_query($mysqli, "UPDATE periksa SET biaya_periksa = '$totalBiaya' WHERE id = '$idPeriksa'");

            if ($updateTotalBiaya) {
                echo '<script>alert("Data berhasil diubah");window.location.href="../../periksaPasien.php"</script>';
            } else {
                echo '<script>alert("Error dalam memperbarui total biaya");window.location.href="../../periksaPasien.php"</script>';
            }
        } else {
            echo '<script>alert("Error dalam menghapus detail sebelumnya");window.location.href="../../periksaPasien.php"</script>';
        }
    } else {
        echo '<script>alert("Data gagal diubah");window.location.href="../../periksaPasien.php"</script>';
    }
}
?>
