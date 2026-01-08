<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// 1. Ambil ID Kamar dari URL dan periksa data
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM kamar WHERE id = ?"); 
$stmt->bind_param('i', $id); 
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

if (!$r) {
    die('<div class="alert alert-danger">Kamar tidak ditemukan.</div>');
}

// 2. Ambil data Tipe Kamar untuk dropdown
$tipe = $conn->query("SELECT id, nama_tipe FROM tipe_kamar");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = trim($_POST['nomor_kamar']);
    $lantai = (int)$_POST['lantai']; // Field Baru
    $id_tipe = (int)$_POST['id_tipe'];
    $status = $_POST['status'];
    $deskripsi = trim($_POST['deskripsi']); // Field Baru
    
    // Validasi sederhana
    if (empty($nomor) || empty($id_tipe) || empty($status) || empty($lantai)) {
        $error = "Semua field wajib diisi.";
    } elseif (!in_array($status, ['tersedia', 'dipesan', 'dibersihkan'])) {
        $error = "Status kamar tidak valid.";
    } else {
        // 3. Update data kamar (Menambahkan kolom lantai dan deskripsi)
        // Format bind_param: siissi (string, int, int, string, string, int-ID)
        $u = $conn->prepare("UPDATE kamar SET nomor_kamar=?, lantai=?, id_tipe=?, status=?, deskripsi=? WHERE id=?");
        $u->bind_param('siissi', $nomor, $lantai, $id_tipe, $status, $deskripsi, $id);
        
        if ($u->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal memperbarui data: " . $u->error;
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
    
    <title>Edit Kamar</title>
</head>

<body class="bg-light p-3">
    
    <div class="container py-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 600px; margin: 0 auto;">
            
            <div class="card-header bg-primary text-white rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-door-closed-fill me-2"></i> Edit Data Kamar #<?php echo htmlspecialchars($r['nomor_kamar']); ?>
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
                            <input name="nomor_kamar" id="nomor_kamar" class="form-control form-control-lg rounded-3" 
                                   value="<?php echo htmlspecialchars($r['nomor_kamar']); ?>" required 
                                   placeholder="Contoh: 101, 2A">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label for="lantai" class="form-label fw-semibold">Lantai</label>
                            <input type="number" name="lantai" id="lantai" class="form-control form-control-lg rounded-3" 
                                   value="<?php echo htmlspecialchars($r['lantai']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="id_tipe" class="form-label fw-semibold">Tipe Kamar</label>
                        <select name="id_tipe" id="id_tipe" class="form-select form-select-lg rounded-3" required>
                            <option value="">-- Pilih Tipe Kamar --</option>
                            <?php 
                            $tipe->data_seek(0); // Reset pointer query tipe
                            while($t = $tipe->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $t['id']; ?>" 
                                        <?php if($t['id'] == $r['id_tipe']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($t['nama_tipe']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold">Status Kamar</label>
                        <select name="status" id="status" class="form-select form-select-lg rounded-3" required>
                            <option value="tersedia" <?php if($r['status'] == 'tersedia') echo 'selected'; ?>>Tersedia (Siap Huni)</option>
                            <option value="dipesan" <?php if($r['status'] == 'dipesan') echo 'selected'; ?>>Dipesan (Occupied)</option>
                            <option value="dibersihkan" <?php if($r['status'] == 'dibersihkan') echo 'selected'; ?>>Dibersihkan (Maintenance)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="deskripsi" class="form-label fw-semibold">Keterangan / Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control rounded-3" rows="3"><?php echo htmlspecialchars($r['deskripsi']); ?></textarea>
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