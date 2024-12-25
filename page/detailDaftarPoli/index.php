<?php
require 'koneksi.php';

// Ambil ID dari parameter URL
$id = $_GET['id'];

// Query untuk mengambil detail periksa
$ambilDetail = mysqli_query($mysqli, "
    SELECT 
        dp.id AS idDetailPeriksa,
        daftar_poli.id AS idDaftarPoli,
        poli.nama_poli,
        dokter.nama AS namaDokter,
        jadwal_periksa.hari,
        DATE_FORMAT(jadwal_periksa.jam_mulai, '%H:%i') AS jamMulai,
        DATE_FORMAT(jadwal_periksa.jam_selesai, '%H:%i') AS jamSelesai,
        daftar_poli.no_antrian,
        p.id AS idPeriksa,
        p.tgl_periksa,
        p.catatan,
        p.biaya_periksa,
        GROUP_CONCAT(o.id) AS idObat,
        GROUP_CONCAT(o.nama_obat SEPARATOR ', ') AS namaObat
    FROM daftar_poli
    INNER JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id
    INNER JOIN dokter ON jadwal_periksa.id_dokter = dokter.id
    INNER JOIN poli ON dokter.id_poli = poli.id
    LEFT JOIN periksa p ON daftar_poli.id = p.id_daftar_poli
    LEFT JOIN detail_periksa dp ON p.id = dp.id_periksa
    LEFT JOIN obat o ON dp.id_obat = o.id
    WHERE daftar_poli.id = '$id'
    GROUP BY daftar_poli.id
");

// Fetch data
$data = mysqli_fetch_assoc($ambilDetail);

// Cek jika data tidak ditemukan
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href = 'daftarPoliklinik.php';</script>";
    exit;
}
?>

<div class="card card-solid">
    <div class="card-body pb-0">
        <div class="row">
            <div class="col-12 d-flex align-items-stretch flex-column">
                <div class="card bg-light d-flex flex-fill">
                    <div class="card-header text-muted border-bottom-0">
                        Detail Periksa
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-7">
                                <h2 class="lead"><b><?php echo htmlspecialchars($data['namaDokter']); ?></b></h2>
                                <h6 class="text-muted text-lg">Poli: <?php echo htmlspecialchars($data['nama_poli']); ?></h6>
                                <h6 class="text-muted text-lg">Hari: <?php echo htmlspecialchars($data['hari']); ?></h6>
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="large">
                                        <span class="fa-li"><i class="fas fa-lg fa-clock"></i></span>
                                        <?php echo htmlspecialchars($data['jamMulai']) . " - " . htmlspecialchars($data['jamSelesai']); ?>
                                    </li>
                                </ul>
                                <br>
                                <p class="text-muted text-lg">
                                    Obat:<br>
                                    <?php
                                    if (!empty($data['namaObat'])) {
                                        $namaObatArray = explode(', ', $data['namaObat']);
                                        foreach ($namaObatArray as $index => $namaObat) {
                                            echo ($index + 1) . ". " . htmlspecialchars($namaObat) . "<br>";
                                        }
                                    } else {
                                        echo "Tidak ada obat yang diresepkan.";
                                    }
                                    ?>
                                </p>
                                <h5 class="text-muted text-lg">
                                    <strong>Biaya Periksa: <?php echo number_format($data['biaya_periksa'], 0, ',', '.'); ?> IDR</strong>
                                </h5>
                            </div>
                            <div class="col-5 d-flex justify-content-center align-items-center flex-column">
                                <h2 class="lead"><b>No Antrian</b></h2>
                                <div class="rounded-lg d-flex justify-content-center align-items-center"
                                    style="height: 60px; width: 60px; background-color: #0284c7; color: white; padding-top: 6px;">
                                    <h1><?php echo htmlspecialchars($data['no_antrian']); ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-left">
                            <a href="daftarPoliklinik.php" class="btn btn-md btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
