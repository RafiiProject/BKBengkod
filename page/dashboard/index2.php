<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already active
}
require 'koneksi.php'; // Database connection file

// Set default username and id_poli for testing (Remove this in production)
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Rafi'; // Example username
}
if (!isset($_SESSION['id_poli'])) {
    $_SESSION['id_poli'] = 1; // Example id_poli
}

$username = $_SESSION['username'];
$id_poli = $_SESSION['id_poli'];

if ($id_poli) {
    // Use prepared statements to fetch nama_poli
    $stmt = $mysqli->prepare("SELECT nama_poli FROM poli WHERE id = ?");
    $stmt->bind_param("i", $id_poli);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nama_poli = $row['nama_poli'];
    } else {
        $nama_poli = "Nama poli tidak ditemukan";
    }
    $stmt->close();
} else {
    $nama_poli = "Poli tidak tersedia";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokter Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .text-white {
            font-weight: 500;
        }

        h1.text-white {
            font-size: 2.2rem;
            font-family: 'Arial', sans-serif;
        }

        h4.text-white {
            font-size: 1.6rem;
            font-family: 'Arial', sans-serif;
        }

        h5.text-white {
            font-size: 1.3rem;
            font-family: 'Arial', sans-serif;
        }

        .info-box-text {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .info-box-number {
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Content Header -->
    <div class="content-header py-5 bg-primary text-white">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0 text-center">Selamat Datang <b>Dokter <?php echo htmlspecialchars($username); ?></b></h1>
                    <h4 class="m-0 text-center">Anda berada di <b>Poli <?php echo htmlspecialchars($nama_poli); ?></b></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content mt-5">
        <div class="container-fluid">
            <!-- Image Section with Marquee -->
            <div class="row mb-4">
                <div class="col-12">
                    <div style="background-image: url('assets/images/gedung.jpg'); background-size: cover; height: 200px; position: relative;">
                        <marquee style="position: absolute; bottom: 0; background-color: rgba(0,0,0,0.5); color: white; width: 100%; padding: 10px;">
                            Pakai Masker - Tetap Jaga Protokol Kesehatan!
                        </marquee>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
