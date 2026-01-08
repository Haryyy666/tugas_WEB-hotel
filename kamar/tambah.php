<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// 1. Ambil data Tipe Kamar untuk dropdown
$tipe = $conn->query("SELECT id, nama_tipe FROM tipe_kamar");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = trim($_POST['nomor_kamar']);
    $lantai = (int)$_POST['lantai']; // Field Baru
    $id_tipe = (int)$_POST['id_tipe'];
    $deskripsi = trim($_POST['deskripsi']); // Field Baru
    
    // Validasi sederhana
    if (empty($nomor) || empty($id_tipe) || empty($lantai)) {
        $error = "Nomor Kamar, Lantai, dan Tipe Kamar wajib diisi.";
    } else {
        // 2. Insert data kamar (Menambahkan kolom lantai dan deskripsi)
        $stmt = $conn->prepare("INSERT INTO kamar (nomor_kamar, lantai, id_tipe, status, deskripsi) VALUES (?, ?, ?, 'tersedia', ?)");
        // s = string, i = integer. Urutan: nomor(s), lantai(i), id_tipe(i), deskripsi(s)
        $stmt->bind_param('siis', $nomor, $lantai, $id_tipe, $deskripsi);
        
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
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 600px; margin: 0 auto;">
            
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
                    
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <label for="nomor_kamar" class="form-label fw-semibold">Nomor Kamar</label>
                            <input name="nomor_kamar" id="nomor_kamar" class="form-control form-control-lg rounded-3" required 
                                   placeholder="Contoh: 101, 2A">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label for="lantai" class="form-label fw-semibold">Lantai</label>
                            <input type="number" name="lantai" id="lantai" class="form-control form-control-lg rounded-3" required 
                                   placeholder="1-10">
                        </div>
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
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Keterangan / Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control rounded-3" rows="3" 
                                  placeholder="Contoh: Dekat lift, Pemandangan laut..."></textarea>
                    </div>
                    
                    <div class="alert alert-info py-2">
                        <small><i class="bi bi-info-circle me-1"></i> Status kamar baru otomatis diset: <strong>Tersedia</strong></small>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-semibold rounded-pill shadow">
                            <i class="bi bi-check-lg me-2"></i> Simpan Kamar
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