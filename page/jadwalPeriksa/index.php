<?php
// Koneksi ke database
require 'koneksi.php';

// Proses update status aktif/nonaktif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['aktif'])) {
    $id = $_POST['id'];
    $aktif = $_POST['aktif'];

    if ($aktif == '1') {
        // Mendapatkan id_dokter dari jadwal yang diaktifkan
        $queryGetDokter = "SELECT id_dokter FROM jadwal_periksa WHERE id = ?";
        $stmtGetDokter = mysqli_prepare($mysqli, $queryGetDokter);
        mysqli_stmt_bind_param($stmtGetDokter, 'i', $id);
        mysqli_stmt_execute($stmtGetDokter);
        mysqli_stmt_bind_result($stmtGetDokter, $id_dokter);
        mysqli_stmt_fetch($stmtGetDokter);
        mysqli_stmt_close($stmtGetDokter);

        // Menonaktifkan semua jadwal lain untuk dokter yang sama
        $queryDeactivate = "UPDATE jadwal_periksa SET aktif = '2' WHERE id_dokter = ?";
        $stmtDeactivate = mysqli_prepare($mysqli, $queryDeactivate);
        mysqli_stmt_bind_param($stmtDeactivate, 'i', $id_dokter);
        mysqli_stmt_execute($stmtDeactivate);
        mysqli_stmt_close($stmtDeactivate);
    }

    // Update status aktif/nonaktif untuk jadwal yang dipilih
    $query = "UPDATE jadwal_periksa SET aktif = ? WHERE id = ?";
    $stmt = mysqli_prepare($mysqli, $query);
    mysqli_stmt_bind_param($stmt, 'si', $aktif, $id);
    $execute = mysqli_stmt_execute($stmt);

    if ($execute) {
        // Redirect ke halaman setelah update
        header("Location: index.php?page=jadwalPeriksa");
        exit;
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . mysqli_error($mysqli);
    }

    mysqli_stmt_close($stmt);
}
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Jadwal Periksa</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?page=home">Home</a></li>
                    <li class="breadcrumb-item active">Jadwal Periksa</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Jadwal Periksa</h3>
                        <div class="card-tools">
                            <!-- Button untuk Modal Tambah Jadwal -->
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addModal">Tambah Jadwal Periksa</button>

                            <!-- Button untuk Modal Lihat Jadwal -->
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#cekJadwal">Lihat Jadwal</button>
                        </div>
                    </div>

                    <!-- Modal Tambah Jadwal -->
                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addModalLabel">Tambah Jadwal Periksa</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="page/jadwalPeriksa/tambahJadwal.php" method="POST">
                                        <input type="hidden" name="id_dokter" value="<?php echo $id_dokter; ?>">
                                        <div class="form-group">
                                            <label for="hari">Hari</label>
                                            <select class="form-control" id="hari" name="hari">
                                                <option value="Senin">Senin</option>
                                                <option value="Selasa">Selasa</option>
                                                <option value="Rabu">Rabu</option>
                                                <option value="Kamis">Kamis</option>
                                                <option value="Jumat">Jumat</option>
                                                <option value="Sabtu">Sabtu</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="jamMulai">Jam Mulai</label>
                                            <input type="time" class="form-control" id="jamMulai" name="jamMulai" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="jamSelesai">Jam Selesai</label>
                                            <input type="time" class="form-control" id="jamSelesai" name="jamSelesai" required>
                                        </div>
                                        <button type="submit" class="btn btn-success">Tambah</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Lihat Jadwal -->
                    <div class="modal fade" id="cekJadwal" tabindex="-1" role="dialog" aria-labelledby="cekJadwalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cekJadwalLabel">Jadwal Poli</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Hari</th>
                                                    <th>Jam Mulai</th>
                                                    <th>Jam Selesai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $ambilDataJadwal = "SELECT id, hari, jam_mulai, jam_selesai FROM jadwal_periksa WHERE id_dokter = '$id_dokter'";
                                                    $result = mysqli_query($mysqli, $ambilDataJadwal);
                                                    $nomor = 1;
                                                    while ($data = mysqli_fetch_assoc($result)) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $nomor++; ?></td>
                                                    <td><?php echo $data['hari']; ?></td>
                                                    <td><?php echo $data['jam_mulai']; ?></td>
                                                    <td><?php echo $data['jam_selesai']; ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Jadwal -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Hari</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th class="text-center">Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $query = "SELECT id, hari, jam_mulai, jam_selesai, aktif FROM jadwal_periksa WHERE id_dokter = '$id_dokter'";
                                $result = mysqli_query($mysqli, $query);
                                while ($data = mysqli_fetch_assoc($result)) {
                                    $status = $data['aktif'] == '1' ? 'Aktif' : 'Nonaktif';
                                    $badgeClass = $data['aktif'] == '1' ? 'badge-success' : 'badge-danger';
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $data['hari']; ?></td>
                                    <td><?php echo $data['jam_mulai']; ?></td>
                                    <td><?php echo $data['jam_selesai']; ?></td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-pill <?php echo $badgeClass; ?>" 
                                            style="font-size: 1.1rem; padding: 10px 15px; width: 120px;">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?php echo $data['id']; ?>">Edit</button>
                                    </td>
                                </tr>

                                <!-- Modal Edit Jadwal -->
                                <div class="modal fade" id="editModal<?php echo $data['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Jadwal Periksa</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="page/jadwalPeriksa/updateJadwal.php" method="POST">
                                                    <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                                                    <div class="form-group">
                                                        <label for="hari">Hari</label>
                                                        <select class="form-control" id="hari" name="hari" required>
                                                            <option value="Senin" <?php echo $data['hari'] == 'Senin' ? 'selected' : ''; ?>>Senin</option>
                                                            <option value="Selasa" <?php echo $data['hari'] == 'Selasa' ? 'selected' : ''; ?>>Selasa</option>
                                                            <option value="Rabu" <?php echo $data['hari'] == 'Rabu' ? 'selected' : ''; ?>>Rabu</option>
                                                            <option value="Kamis" <?php echo $data['hari'] == 'Kamis' ? 'selected' : ''; ?>>Kamis</option>
                                                            <option value="Jumat" <?php echo $data['hari'] == 'Jumat' ? 'selected' : ''; ?>>Jumat</option>
                                                            <option value="Sabtu" <?php echo $data['hari'] == 'Sabtu' ? 'selected' : ''; ?>>Sabtu</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="jamMulai">Jam Mulai</label>
                                                        <input type="time" class="form-control" id="jamMulai" name="jamMulai" value="<?php echo $data['jam_mulai']; ?>" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="jamSelesai">Jam Selesai</label>
                                                        <input type="time" class="form-control" id="jamSelesai" name="jamSelesai" value="<?php echo $data['jam_selesai']; ?>" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="aktif">Status</label>
                                                        <select class="form-control" id="aktif" name="aktif" required>
                                                            <option value="1" <?php echo $data['aktif'] == '1' ? 'selected' : ''; ?>>Aktif</option>
                                                            <option value="2" <?php echo $data['aktif'] == '2' ? 'selected' : ''; ?>>Nonaktif</option>
                                                        </select>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
