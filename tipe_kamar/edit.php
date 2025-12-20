<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

$error = '';

// 1. Ambil ID Tipe Kamar dari URL dan periksa data
$id = (int) $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM tipe_kamar WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

if (!$r) {
    die('<div class="alert alert-danger">Tipe Kamar tidak ditemukan.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_tipe']);
    $fasilitas = trim($_POST['fasilitas']);
    $harga = (int) $_POST['harga'];
    
    // Validasi sederhana
    if (empty($nama) || $harga <= 0) {
        $error = "Nama Tipe dan Harga wajib diisi dan harus lebih dari nol.";
    } else {
        // 2. Update data tipe kamar
        // Perhatikan tipe data: ssii (string, string, integer, integer)
        $u = $conn->prepare("UPDATE tipe_kamar SET nama_tipe=?, fasilitas=?, harga=? WHERE id=?");
        $u->bind_param('ssii', $nama, $fasilitas, $harga, $id);
        
        if ($u->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal memperbarui tipe kamar: " . $u->error;
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
    
    <title>Edit Tipe Kamar</title>
</head>

<body class="bg-light p-3">
    
    <div class="container py-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 550px; margin: 0 auto;">
            
            <div class="card-header bg-primary text-white rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-tag-fill me-2"></i> Edit Tipe Kamar
                </h3>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal!</strong> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    
                    <div class="mb-4">
                        <label for="nama_tipe" class="form-label fw-semibold">Nama Tipe</label>
                        <input name="nama_tipe" id="nama_tipe" class="form-control form-control-lg rounded-3" 
                               value="<?php echo htmlspecialchars($r['nama_tipe']); ?>" required
                               placeholder="Contoh: Deluxe, Suite, VVIP">
                    </div>
                    
                    <div class="mb-4">
                        <label for="harga" class="form-label fw-semibold">Harga (Per Malam)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text rounded-start-3 fw-bold">Rp</span>
                            <input name="harga" id="harga" type="number" class="form-control rounded-end-3" 
                                   value="<?php echo htmlspecialchars($r['harga']); ?>" required
                                   min="1000" placeholder="Contoh: 500000">
                        </div>
                        <div class="form-text">Masukkan harga tanpa titik atau koma.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="fasilitas" class="form-label fw-semibold">Fasilitas (Daftar Fasilitas)</label>
                        <textarea name="fasilitas" id="fasilitas" class="form-control rounded-3" rows="4"
                                  placeholder="Contoh: AC, TV 32 inci, Kamar Mandi Dalam, Sarapan Gratis">
                            <?php echo htmlspecialchars($r['fasilitas']); ?>
                        </textarea>
                    </div>

                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary btn-lg px-4 fw-semibold rounded-pill shadow">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
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