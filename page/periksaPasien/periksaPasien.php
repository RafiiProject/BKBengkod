<?php
require '../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = $_POST['id'];
    $tanggalPeriksa = $_POST['tanggal_periksa'];
    $catatan = $_POST['catatan'];
    $arrayObat = isset($_POST['obat']) ? $_POST['obat'] : []; // Pastikan arrayObat selalu terdefinisi

    // Update status pasien pada daftar_poli
    $updateStatus = "UPDATE daftar_poli SET status_periksa = '1' WHERE id = '$id'";
    $query = mysqli_query($mysqli, $updateStatus);

    if ($query) {
        // Inisialisasi harga awal pemeriksaan
        $hargaAwal = 150000;

        // Insert data pemeriksaan awal dengan harga default
        $insertPeriksa = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES ('$id', '$tanggalPeriksa', '$catatan', $hargaAwal)";
        $queryInsertPeriksa = mysqli_query($mysqli, $insertPeriksa);

        if ($queryInsertPeriksa) {
            // Ambil data terakhir dari tabel periksa
            $getLastData = "SELECT * FROM periksa ORDER BY id DESC LIMIT 1";
            $queryGetLastData = mysqli_query($mysqli, $getLastData);
            $getIdPeriksa = mysqli_fetch_assoc($queryGetLastData);

            $idPeriksa = $getIdPeriksa['id'];

            // Inisialisasi total biaya dengan harga awal
            $totalBiaya = $hargaAwal;

            foreach ($arrayObat as $obat) {
                // Insert detail periksa (obat yang dipilih)
                $inserDetailPeriksa = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES ('$idPeriksa', '$obat')";
                $queryDetailPeriksa = mysqli_query($mysqli, $inserDetailPeriksa);

                // Ambil harga obat dari database
                $getHargaObat = "SELECT harga FROM obat WHERE id = '$obat'";
                $queryHargaObat = mysqli_query($mysqli, $getHargaObat);
                $hargaObat = mysqli_fetch_assoc($queryHargaObat)['harga'];

                // Tambahkan harga obat ke total biaya
                $totalBiaya += $hargaObat;

                if (!$queryDetailPeriksa) {
                    echo '<script>alert("Error dalam menambahkan detail periksa");window.location.href="../../periksaPasien.php"</script>';
                    exit; // Keluar dari skrip jika terjadi kesalahan
                }
            }

            // Update total biaya pada data periksa
            $updateTotalBiaya = "UPDATE periksa SET biaya_periksa = '$totalBiaya' WHERE id = '$idPeriksa'";
            $queryUpdateTotalBiaya = mysqli_query($mysqli, $updateTotalBiaya);

            if ($queryUpdateTotalBiaya) {
                echo '<script>alert("Pasien telah diperiksa dengan sukses");window.location.href="../../periksaPasien.php"</script>';
            } else {
                echo '<script>alert("Error dalam memperbarui total biaya");window.location.href="../../periksaPasien.php"</script>';
            }
        } else {
            echo '<script>alert("Error dalam menambahkan data pemeriksaan");window.location.href="../../periksaPasien.php"</script>';
        }
    } else {
        echo '<script>alert("Error dalam memperbarui status pasien");window.location.href="../../periksaPasien.php"</script>';
    }
}
?>
