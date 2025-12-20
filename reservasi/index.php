<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// Query untuk mengambil data reservasi
$q = $conn->query("SELECT 
    r.id, 
    r.check_in, 
    r.check_out, 
    r.total_harga, 
    r.status_reservasi,
    k.nomor_kamar, 
    t.nama AS nama_tamu 
FROM reservasi r 
JOIN kamar k ON r.id_kamar = k.id 
JOIN tamu t ON r.id_tamu = t.id 
ORDER BY r.created_at DESC");

// Fungsi helper untuk menentukan warna badge berdasarkan status reservasi
function get_reservasi_status_badge($status) {
    switch ($status) {
        case 'pending':
            return 'bg-warning text-dark';
        case 'check_in':
            return 'bg-success';
        case 'selesai':
            return 'bg-primary';
        case 'batal':
            return 'bg-danger';
        default:
            return 'bg-secondary';
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
    
    <title>Daftar Reservasi</title>
</head>

<body class="bg-light p-3">
    <div class="container-fluid py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h3 fw-bold text-dark">
                <i class="bi bi-calendar-check-fill me-2 text-warning"></i> Daftar Reservasi
            </h1>
            <a href="tambah.php" class="btn btn-success btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Tambah Reservasi
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 small">
                        <thead class="table-warning">
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col">Kamar</th>
                                <th scope="col">Tamu</th>
                                <th scope="col">Check-In</th>
                                <th scope="col">Check-Out</th>
                                <th scope="col" class="text-end">Total Harga</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($r = $q->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo $i++; ?></td>
                                <td class="fw-semibold align-middle"><?php echo htmlspecialchars($r['nomor_kamar']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($r['nama_tamu']); ?></td>
                                <td class="align-middle"><?php echo date('d M Y', strtotime($r['check_in'])); ?></td>
                                <td class="align-middle"><?php echo date('d M Y', strtotime($r['check_out'])); ?></td>
                                <td class="text-end fw-bold align-middle">
                                    Rp <?php echo number_format($r['total_harga'], 0, ',', '.'); ?>
                                </td>
                                
                                <td class="text-center align-middle">
                                    <span class="badge <?php echo get_reservasi_status_badge($r['status_reservasi']); ?> fw-semibold p-2">
                                        <?php echo str_replace('_', ' ', ucfirst($r['status_reservasi'])); ?>
                                    </span>
                                </td>
                                
                                <td class="text-center align-middle">
                                    <a href="edit.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary rounded-pill me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger rounded-pill" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus reservasi ini? ID: <?php echo $r['id']; ?>')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($q->num_rows === 0): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted p-4">
                                        Belum ada data Reservasi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="../dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>