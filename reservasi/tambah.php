<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

$error = '';

// Ambil data kamar yang statusnya 'tersedia' dan data tamu
$kamar = $conn->query("SELECT k.id, k.nomor_kamar, t.nama_tipe, t.harga 
                      FROM kamar k 
                      JOIN tipe_kamar t ON k.id_tipe=t.id 
                      WHERE k.status='tersedia'
                      ORDER BY k.nomor_kamar"); 
$tamu = $conn->query("SELECT id, nama FROM tamu ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kamar = (int)$_POST['id_kamar']; 
    $id_tamu = (int)$_POST['id_tamu'];
    $check_in = $_POST['check_in']; 
    $check_out = $_POST['check_out'];
    
    // Validasi Tanggal
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);

    if ($date1 >= $date2) {
        $error = "Tanggal Check Out harus setelah Tanggal Check In.";
    } elseif ($kamar->num_rows === 0) {
        $error = "Tidak ada kamar tersedia untuk reservasi.";
    } elseif ($tamu->num_rows === 0) {
        $error = "Tidak ada data tamu terdaftar. Harap tambahkan tamu terlebih dahulu.";
    } else {
        $diff = $date1->diff($date2); 
        $nights = max(1, $diff->days);
        
        // 1. Ambil harga kamar
        $p = $conn->prepare("SELECT t.harga FROM kamar k JOIN tipe_kamar t ON k.id_tipe=t.id WHERE k.id=?"); 
        $p->bind_param('i',$id_kamar); 
        $p->execute(); 
        $res = $p->get_result()->fetch_assoc();
        
        $total = (int)$res['harga'] * $nights;
        
        // 2. Insert reservasi
        // Status default: 'pending'
        $ins = $conn->prepare("INSERT INTO reservasi (id_kamar, id_tamu, check_in, check_out, total_harga, status_reservasi) VALUES (?,?,?,?,?, 'pending')");
        // Tipe data: iissi (integer, integer, string, string, integer)
        $ins->bind_param('iissi', $id_kamar, $id_tamu, $check_in, $check_out, $total);
        
        if ($ins->execute()) {
            // 3. Set kamar jadi 'dipesan'
            // Menggunakan prepare statement untuk keamanan, meskipun ID adalah integer yang sudah divalidasi
            $upd_kamar = $conn->prepare("UPDATE kamar SET status='dipesan' WHERE id=?");
            $upd_kamar->bind_param('i', $id_kamar);
            $upd_kamar->execute();
            
            header('Location: index.php'); 
            exit;
        } else {
            $error = "Gagal menyimpan reservasi: " . $ins->error;
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
    
    <title>Tambah Reservasi</title>
</head>

<body class="bg-light p-3">
    
    <div class="container py-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 650px; margin: 0 auto;">
            
            <div class="card-header bg-success text-white rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-plus-fill me-2"></i> Buat Reservasi Baru
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
                        <label for="id_kamar" class="form-label fw-semibold">Pilih Kamar Tersedia</label>
                        <select name="id_kamar" id="id_kamar" class="form-select form-select-lg rounded-3" required>
                            <option value="" disabled selected>-- Pilih Kamar --</option>
                            <?php while($k=$kamar->fetch_assoc()): ?>
                                <option value="<?php echo $k['id']; ?>">
                                    <?php echo htmlspecialchars($k['nomor_kamar'].' - '.$k['nama_tipe'].' (Rp '.number_format($k['harga'], 0, ',', '.').'/malam)'); ?>
                                </option>
                            <?php endwhile; ?>
                            <?php if ($kamar->num_rows === 0): ?>
                                <option value="" disabled>-- TIDAK ADA KAMAR TERSEDIA --</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Hanya kamar dengan status 'Tersedia' yang muncul.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="id_tamu" class="form-label fw-semibold">Pilih Tamu</label>
                        <select name="id_tamu" id="id_tamu" class="form-select form-select-lg rounded-3" required>
                            <option value="" disabled selected>-- Pilih Tamu --</option>
                            <?php while($t=$tamu->fetch_assoc()): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['nama']); ?></option>
                            <?php endwhile; ?>
                            <?php if ($tamu->num_rows === 0): ?>
                                <option value="" disabled>-- TIDAK ADA DATA TAMU --</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Pastikan tamu sudah terdaftar di modul Tamu.</div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="check_in" class="form-label fw-semibold">Tanggal Check In</label>
                            <input name="check_in" id="check_in" type="date" class="form-control form-control-lg rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label for="check_out" class="form-label fw-semibold">Tanggal Check Out</label>
                            <input name="check_out" id="check_out" type="date" class="form-control form-control-lg rounded-3" required>
                        </div>
                        <div class="form-text mt-2">Total harga akan dihitung otomatis berdasarkan durasi menginap.</div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-semibold rounded-pill shadow">
                            <i class="bi bi-check-circle me-2"></i> Simpan Reservasi
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