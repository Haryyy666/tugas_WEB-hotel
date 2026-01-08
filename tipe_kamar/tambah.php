<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// Inisialisasi variabel untuk pesan error
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data
    $nama = trim($_POST['nama_tipe']);
    $fasilitas = trim($_POST['fasilitas']);
    
    // Validasi angka-angka
    $harga = filter_var($_POST['harga'], FILTER_VALIDATE_INT);
    $kapasitas = filter_var($_POST['kapasitas'], FILTER_VALIDATE_INT);
    $jumlah_kamar = filter_var($_POST['jumlah_kamar'], FILTER_VALIDATE_INT);

    if (empty($nama)) {
        $error = "Nama Tipe Kamar tidak boleh kosong.";
    } elseif ($harga === false || $harga < 0) {
        $error = "Harga harus berupa angka positif.";
    } elseif ($kapasitas === false || $kapasitas <= 0) {
        $error = "Kapasitas minimal 1 orang.";
    } elseif ($jumlah_kamar === false || $jumlah_kamar < 0) {
        $error = "Jumlah kamar tidak valid.";
    } else {
        // SQL UPDATED: Menambahkan kolom kapasitas dan jumlah_kamar
        $stmt = $conn->prepare("INSERT INTO tipe_kamar (nama_tipe, fasilitas, harga, kapasitas, jumlah_kamar) VALUES (?, ?, ?, ?, ?)");
        // bind_param updated: ssiii (string, string, integer, integer, integer)
        $stmt->bind_param('ssiii', $nama, $fasilitas, $harga, $kapasitas, $jumlah_kamar);
        
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal menyimpan data ke database: " . $conn->error;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Tambah Tipe Kamar</title>
</head>

<body class="bg-light d-flex justify-content-center align-items-center min-vh-100 p-3">
    
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-door-open-fill me-2"></i> Tambah Tipe Kamar
                </h3>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="nama_tipe" class="form-label fw-semibold">Nama Tipe</label>
                        <input name="nama_tipe" id="nama_tipe" class="form-control form-control-lg rounded-3" 
                               placeholder="Contoh: Standar, Deluxe, Suite" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fasilitas" class="form-label fw-semibold">Fasilitas</label>
                        <textarea name="fasilitas" id="fasilitas" class="form-control rounded-3" rows="3"
                                  placeholder="Masukkan daftar fasilitas (misal: AC, Wi-Fi, TV Kabel)"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kapasitas" class="form-label fw-semibold">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" id="kapasitas" class="form-control rounded-3" 
                                   placeholder="Contoh: 2" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="jumlah_kamar" class="form-label fw-semibold">Stok Kamar (Unit)</label>
                            <input type="number" name="jumlah_kamar" id="jumlah_kamar" class="form-control rounded-3" 
                                   placeholder="Contoh: 10" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="harga" class="form-label fw-semibold">Harga (per malam)</label>
                        <div class="input-group input-group-lg rounded-3">
                            <span class="input-group-text rounded-start-3 fw-bold">Rp</span>
                            <input name="harga" id="harga" type="number" class="form-control rounded-end-3" 
                                   placeholder="Contoh: 450000" min="0" required>
                        </div>
                    </div>
                    
                    <hr class="mt-4 mb-3">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary btn-lg px-4 fw-semibold rounded-pill">
                            <i class="bi bi-save me-2"></i> Simpan Tipe
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                            <i class="bi bi-x-lg me-2"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>