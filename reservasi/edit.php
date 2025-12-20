<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

$error = '';
$success = '';

// 1. Ambil ID Reservasi dari URL
$id = (int) $_GET['id'];
$s = $conn->prepare("SELECT * FROM reservasi WHERE id=?");
$s->bind_param('i', $id);
$s->execute();
$r = $s->get_result()->fetch_assoc();

if (!$r) {
    die('<div class="alert alert-danger">Reservasi tidak ditemukan.</div>');
}

// 2. Ambil data dropdown
// Hanya tampilkan kamar yang 'tersedia' atau kamar yang sedang direservasi ini.
$kamar = $conn->query("SELECT k.id, k.nomor_kamar, t.nama_tipe 
                      FROM kamar k 
                      JOIN tipe_kamar t ON k.id_tipe = t.id 
                      WHERE k.status = 'tersedia' OR k.id = {$r['id_kamar']}
                      ORDER BY k.nomor_kamar");
$tamu = $conn->query("SELECT id, nama FROM tamu ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kamar = (int) $_POST['id_kamar'];
    $id_tamu = (int) $_POST['id_tamu'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $status = $_POST['status'];
    
    // Hitung jumlah malam
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);

    if ($date1 >= $date2) {
        $error = "Tanggal Check Out harus setelah Tanggal Check In.";
    } else {
        $diff = $date1->diff($date2);
        $nights = max(1, $diff->days); // Minimal 1 malam
        
        // Ambil harga kamar berdasarkan ID Kamar yang baru dipilih
        $p = $conn->prepare("SELECT t.harga FROM kamar k JOIN tipe_kamar t ON k.id_tipe=t.id WHERE k.id=?");
        $p->bind_param('i', $id_kamar);
        $p->execute();
        $res = $p->get_result()->fetch_assoc();
        
        if (!$res) {
            $error = "Gagal mendapatkan harga tipe kamar.";
        } else {
            $total = (int) $res['harga'] * $nights;
            
            // Lakukan Update
            $u = $conn->prepare("UPDATE reservasi SET id_kamar=?, id_tamu=?, check_in=?, check_out=?, total_harga=?, status_reservasi=? WHERE id=?");
            // Tipe data: isssisi (integer, string, string, string, integer, string, integer)
            $u->bind_param('isssisi', $id_kamar, $id_tamu, $check_in, $check_out, $total, $status, $id);
            
            if ($u->execute()) {
                // Redirect setelah sukses update
                header('Location: index.php?msg=updated');
                exit;
            } else {
                $error = "Gagal memperbarui reservasi: " . $u->error;
            }
        }
    }
}

// Ambil harga kamar yang sedang direservasi untuk ditampilkan di form
$p = $conn->prepare("SELECT t.harga FROM kamar k JOIN tipe_kamar t ON k.id_tipe=t.id WHERE k.id=?");
$p->bind_param('i', $r['id_kamar']);
$p->execute();
$current_price = $p->get_result()->fetch_assoc()['harga'] ?? 0;

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <title>Edit Reservasi</title>
</head>

<body class="bg-light p-3">
    
    <div class="container py-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 700px; margin: 0 auto;">
            
            <div class="card-header bg-warning text-dark rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-check-fill me-2"></i> Edit Reservasi
                </h3>
                <p class="text-muted mb-0 small">ID Reservasi: #<?php echo $r['id']; ?></p>
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
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="id_kamar" class="form-label fw-semibold">Pilih Kamar</label>
                                <select name="id_kamar" id="id_kamar" class="form-select form-select-lg rounded-3" required>
                                    <option value="" disabled>-- Pilih Kamar --</option>
                                    <?php while ($k = $kamar->fetch_assoc()): ?>
                                        <option value="<?php echo $k['id']; ?>" 
                                                <?php if ($k['id'] == $r['id_kamar']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($k['nomor_kamar']); ?> (<?php echo htmlspecialchars($k['nama_tipe']); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="form-text">Harga per malam saat ini: 
                                    <strong class="text-success">Rp <?php echo number_format($current_price, 0, ',', '.'); ?></strong>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="id_tamu" class="form-label fw-semibold">Pilih Tamu</label>
                                <select name="id_tamu" id="id_tamu" class="form-select form-select-lg rounded-3" required>
                                    <option value="" disabled>-- Pilih Tamu --</option>
                                    <?php while ($t = $tamu->fetch_assoc()): ?>
                                        <option value="<?php echo $t['id']; ?>" 
                                                <?php if ($t['id'] == $r['id_tamu']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($t['nama']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="check_in" class="form-label fw-semibold">Tanggal Check In</label>
                                <input name="check_in" id="check_in" type="date" class="form-control form-control-lg rounded-3"
                                       value="<?php echo $r['check_in']; ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="check_out" class="form-label fw-semibold">Tanggal Check Out</label>
                                <input name="check_out" id="check_out" type="date" class="form-control form-control-lg rounded-3"
                                       value="<?php echo $r['check_out']; ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold">Status Reservasi</label>
                                <select name="status" id="status" class="form-select form-select-lg rounded-3" required>
                                    <option value="pending" <?php if ($r['status_reservasi'] == 'pending') echo 'selected'; ?>>Pending (Menunggu Pembayaran)</option>
                                    <option value="check_in" <?php if ($r['status_reservasi'] == 'check_in') echo 'selected'; ?>>Check In (Sedang Menginap)</option>
                                    <option value="selesai" <?php if ($r['status_reservasi'] == 'selesai') echo 'selected'; ?>>Selesai (Sudah Check Out)</option>
                                    <option value="batal" <?php if ($r['status_reservasi'] == 'batal') echo 'selected'; ?>>Batal</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">

                    <p class="text-center h5 text-primary">
                        <i class="bi bi-info-circle me-2"></i> Total Harga Reservasi akan **diperbarui secara otomatis** setelah Anda klik Simpan.
                    </p>
                    <p class="text-center h3 fw-bold text-success mb-5">
                       Total Sebelumnya: Rp <?php echo number_format($r['total_harga'] ?? 0, 0, ',', '.'); ?>
                    </p>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-warning btn-lg px-4 fw-bold rounded-pill shadow text-dark">
                            <i class="bi bi-arrow-repeat me-2"></i> Update Reservasi
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