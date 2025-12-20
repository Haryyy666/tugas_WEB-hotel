<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// 1. Ambil data Tipe Kamar untuk dropdown
$tipe = $conn->query("SELECT id, nama_tipe FROM tipe_kamar");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = trim($_POST['nomor_kamar']);
    $id_tipe = (int)$_POST['id_tipe'];
    
    // Validasi sederhana
    if (empty($nomor) || empty($id_tipe)) {
        $error = "Nomor Kamar dan Tipe Kamar wajib diisi.";
    } else {
        // 2. Insert data kamar (Status default: 'tersedia')
        $stmt = $conn->prepare("INSERT INTO kamar (nomor_kamar, id_tipe, status) VALUES (?, ?, 'tersedia')");
        $stmt->bind_param('si', $nomor, $id_tipe);
        
        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal menyimpan data kamar: " . $stmt->error;
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
    
    <title>Tambah Kamar</title>
</head>

<body class="bg-light p-3">
    
    <div class="container py-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 550px; margin: 0 auto;">
            
            <div class="card-header bg-success text-white rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-door-open me-2"></i> Tambah Kamar Baru
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
                        <label for="nomor_kamar" class="form-label fw-semibold">Nomor Kamar</label>
                        <input name="nomor_kamar" id="nomor_kamar" class="form-control form-control-lg rounded-3" required 
                               placeholder="Contoh: 101, 2A, VVIP-05">
                    </div>
                    
                    <div class="mb-4">
                        <label for="id_tipe" class="form-label fw-semibold">Tipe Kamar</label>
                        <select name="id_tipe" id="id_tipe" class="form-select form-select-lg rounded-3" required>
                            <option value="" disabled selected>-- Pilih Tipe Kamar --</option>
                            <?php while($r = $tipe->fetch_assoc()): ?>
                                <option value="<?php echo $r['id']; ?>">
                                    <?php echo htmlspecialchars($r['nama_tipe']); ?>
                                </option>
                            <?php endwhile; ?>
                            <?php if ($tipe->num_rows === 0): ?>
                                <option value="" disabled>-- Belum ada Tipe Kamar, harap buat dulu --</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Status default kamar yang baru ditambahkan adalah 'Tersedia'.</div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-semibold rounded-pill shadow">
                            <i class="bi bi-plus-lg me-2"></i> Simpan
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