<?php
require 'koneksi.php'; // Pastikan koneksi ke database sudah benar

// Tidak menggunakan $id_dokter karena struktur tabel tidak menunjukkannya
?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Daftar Periksa Pasien</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?page=home">Home</a></li>
                    <li class="breadcrumb-item active">Daftar Periksa</li>
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
                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Daftar Poli</th>
                                    <th>Tanggal Periksa</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $no = 1;

                                    // Query untuk mendapatkan data dari tabel periksa
                                    $query = "
                                        SELECT 
                                            id_daftar_poli, 
                                            tgl_periksa, 
                                            catatan 
                                        FROM 
                                            periksa
                                    ";
                                    $result = mysqli_query($mysqli, $query);

                                    if (!$result) {
                                        echo "<tr><td colspan='5'>Error pada query: " . htmlspecialchars(mysqli_error($mysqli)) . "</td></tr>";
                                    } else {
                                        while ($data = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($data['id_daftar_poli']); ?></td>
                                    <td><?php echo htmlspecialchars($data['tgl_periksa']); ?></td>
                                    <td><?php echo htmlspecialchars($data['catatan']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning edit-btn" data-toggle="modal"
                                            data-target="#editModal<?php echo $data['id_daftar_poli']; ?>">Edit</button>
                                        <div class="modal fade" id="editModal<?php echo $data['id_daftar_poli']; ?>" tabindex="-1"
                                            role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel">Edit Periksa Pasien</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="pages/periksaPasien/editPeriksa.php" method="post">
                                                            <input type="hidden" name="id_daftar_poli"
                                                                value="<?php echo htmlspecialchars($data['id_daftar_poli']); ?>">
                                                            <div class="form-group">
                                                                <label for="tanggal_periksa">Tanggal Periksa</label>
                                                                <input type="datetime-local" class="form-control"
                                                                    id="tanggal_periksa" name="tanggal_periksa" required
                                                                    value="<?php echo htmlspecialchars($data['tgl_periksa']); ?>">
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label for="catatan">Catatan</label>
                                                                <textarea class="form-control" rows="3" id="catatan"
                                                                    name="catatan" required><?php echo htmlspecialchars($data['catatan']); ?></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-success">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
