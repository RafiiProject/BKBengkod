<?php
require '../../koneksi.php';

// Ambil data poli untuk dropdown
$queryPoli = "SELECT id, nama FROM poli WHERE aktif = '1'";
$resultPoli = mysqli_query($mysqli, $queryPoli);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $no_rm = $_POST['no_rm'];
    $idJadwal = $_POST['jadwal'];
    $keluhan = $_POST['keluhan'];
    $noAntrian = 0;

    $cariPasien = "SELECT * FROM pasien WHERE no_rm = '$no_rm'";
    $query = mysqli_query($mysqli, $cariPasien);
    $data = mysqli_fetch_assoc($query);
    $idPasien = $data['id'];

    $cekData = "SELECT * FROM daftar_poli";
    $queryCekData = mysqli_query($mysqli, $cekData);
    if (mysqli_num_rows($queryCekData) > 0) {
        $cekNoAntrian = "SELECT * FROM daftar_poli WHERE id_jadwal = '$idJadwal' ORDER BY no_antrian DESC LIMIT 1";
        $queryNoAntrian = mysqli_query($mysqli, $cekNoAntrian);
        $dataPoli = mysqli_fetch_assoc($queryNoAntrian);
        $antrianTerakhir = (int)$dataPoli['no_antrian'];
        $antrianBaru = $antrianTerakhir + 1;

        $daftarPoli = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status_periksa) 
                       VALUES ('$idPasien', '$idJadwal', '$keluhan', '$antrianBaru', '0')";
        $queryDaftarPoli = mysqli_query($mysqli, $daftarPoli);
        if ($queryDaftarPoli) {
            echo '<script>alert("Berhasil mendaftar poli");window.location.href="../../daftarPoliklinik.php";</script>';
        } else {
            echo '<script>alert("Gagal mendaftar poli");window.location.href="../../daftarPoliklinik.php";</script>';
        }
    } else {
        $noAntrian = 1;
        $daftarPoli = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status_periksa) 
                       VALUES ('$idPasien', '$idJadwal', '$keluhan', '$noAntrian', '0')";
        $queryDaftarPoli = mysqli_query($mysqli, $daftarPoli);
        if ($queryDaftarPoli) {
            echo '<script>alert("Berhasil mendaftar poli");window.location.href="../../daftarPoliklinik.php";</script>';
        } else {
            echo '<script>alert("Gagal mendaftar poli");window.location.href="../../daftarPoliklinik.php";</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Poli</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Form Pendaftaran Poli</h1>
    <form action="daftarPoli.php" method="POST">
        <div class="form-group">
            <label for="no_rm">Nomor Rekam Medis</label>
            <input type="text" name="no_rm" id="no_rm" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="poli">Poli</label>
            <select name="poli" id="poli" class="form-control" required>
                <option value="" disabled selected>Pilih Poli</option>
                <?php
                while ($poli = mysqli_fetch_assoc($resultPoli)) {
                    echo '<option value="' . $poli['id'] . '">' . $poli['nama'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="jadwal">Jadwal Periksa</label>
            <select name="jadwal" id="jadwal" class="form-control" required>
                <option value="" disabled selected>Pilih Jadwal</option>
            </select>
        </div>
        <div class="form-group">
            <label for="keluhan">Keluhan</label>
            <textarea name="keluhan" id="keluhan" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Daftar</button>
    </form>

    <script>
        $(document).ready(function() {
            $('#poli').change(function() {
                var poliId = $(this).val();
                if (poliId) {
                    $.ajax({
                        url: 'getJadwal.php',
                        method: 'POST',
                        data: { poliId: poliId },
                        success: function(data) {
                            $('#jadwal').html(data);
                        }
                    });
                } else {
                    $('#jadwal').html('<option value="" disabled selected>Pilih Jadwal</option>');
                }
            });
        });
    </script>
</body>
</html>
