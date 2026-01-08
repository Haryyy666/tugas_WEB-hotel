<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// 1. Ambil ID Tamu dari URL dan periksa data
$id = (int)$_GET['id'];
$s = $conn->prepare("SELECT * FROM tamu WHERE id=?"); 
$s->bind_param('i', $id); 
$s->execute(); 
$r = $s->get_result()->fetch_assoc();

if (!$r) {
    die('<div class="alert alert-danger">Data Tamu tidak ditemukan.</div>');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']); 
    $no_hp = trim($_POST['no_hp']); 
    $alamat = trim($_POST['alamat']); 
    $nik = trim($_POST['nik']);
    $email = trim($_POST['email']); // Field Baru
    
    // Validasi sederhana
    if (empty($nama) || empty($nik)) {
        $error = "Nama dan NIK wajib diisi.";
    } else {
        // 2. Update data tamu (Query diperbarui menambahkan email)
        // Tipe data: sssssi (5 string, 1 integer untuk ID)
        $u = $conn->prepare("UPDATE tamu SET nama=?, no_hp=?, alamat=?, nik=?, email=? WHERE id=?"); 
        $u->bind_param('sssssi', $nama, $no_hp, $alamat, $nik, $email, $id);
        
        if ($u->execute()) {
            header('Location: index.php?msg=updated');
            exit;
        } else {
            $error = "Gagal memperbarui data tamu. Error: " . $u->error;
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
    
    <title>Edit Tamu</title>
</head>

<body class="bg-light p-3">
    
    <div class="container py-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 700px; margin: 0 auto;">
            
            <div class="card-header bg-primary text-white rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-person-lines-fill me-2"></i> Edit Data Tamu: <?php echo htmlspecialchars($r['nama']); ?>
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
                        <div class="col-md-6 mb-4">
                            <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                            <input name="nama" id="nama" class="form-control form-control-lg rounded-3" required 
                                   value="<?php echo htmlspecialchars($r['nama']); ?>" 
                                   placeholder="Nama lengkap tamu">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                            <input name="no_hp" id="no_hp" class="form-control form-control-lg rounded-3" 
                                   value="<?php echo htmlspecialchars($r['no_hp']); ?>"
                                   placeholder="Contoh: 0812xxxxxx">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="nik" class="form-label fw-semibold">NIK (Nomor Induk Kependudukan)</label>
                            <input name="nik" id="nik" class="form-control form-control-lg rounded-3" 
                                   value="<?php echo htmlspecialchars($r['nik']); ?>" 
                                   placeholder="16 digit NIK" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label fw-semibold">Alamat Email</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg rounded-3" 
                                   value="<?php echo htmlspecialchars($r['email']); ?>"
                                   placeholder="contoh@mail.com">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="alamat" class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control rounded-3" rows="3"
                                  placeholder="Alamat lengkap tamu"><?php echo htmlspecialchars($r['alamat']); ?></textarea>
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