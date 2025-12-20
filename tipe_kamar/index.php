<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// 1. Query untuk mengambil semua data tipe kamar (kolom foto diabaikan)
$q = $conn->query("SELECT id, nama_tipe, fasilitas, harga FROM tipe_kamar ORDER BY harga DESC");

// 2. Query untuk menghitung jumlah kamar yang menggunakan setiap tipe (RELASI PENTING)
// Asumsi tabel kamar memiliki kolom 'id_tipe'
$count_kamar = $conn->query("
    SELECT id_tipe, COUNT(id) as total_kamar
    FROM kamar
    GROUP BY id_tipe
");

$kamar_counts = [];
while ($row = $count_kamar->fetch_assoc()) {
    $kamar_counts[$row['id_tipe']] = $row['total_kamar'];
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <title>Daftar Tipe Kamar</title>
</head>

<body class="bg-light p-3">
    <div class="container-fluid py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h3 fw-bold text-dark">
                <i class="bi bi-tags-fill me-2 text-primary"></i> Daftar Tipe Kamar
            </h1>
            <a href="tambah.php" class="btn btn-success btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Tambah Tipe
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 small">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col">Nama Tipe</th>
                                <th scope="col">Fasilitas Utama</th>
                                <th scope="col" class="text-end">Harga / Malam</th>
                                <th scope="col" class="text-center">Total Kamar</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($r = $q->fetch_assoc()): 
                                $id_tipe = $r['id'];
                                // Mengambil hitungan kamar, default 0
                                $total_kamar = $kamar_counts[$id_tipe] ?? 0;
                            ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo $i++; ?></td>
                                <td class="fw-bold align-middle"><?php echo htmlspecialchars($r['nama_tipe']); ?></td>
                                
                                <td class="text-muted small align-middle">
                                    <?php 
                                    // Memotong fasilitas agar tidak terlalu panjang di tabel
                                    echo htmlspecialchars(substr($r['fasilitas'], 0, 60)); 
                                    echo (strlen($r['fasilitas']) > 60) ? '...' : ''; 
                                    ?>
                                </td>
                                
                                <td class="text-end fw-bold align-middle text-success">
                                    Rp <?php echo number_format($r['harga'], 0, ',', '.'); ?>
                                </td>
                                
                                <td class="text-center align-middle">
                                    <span class="badge bg-secondary p-2"><?php echo $total_kamar; ?></span>
                                </td>
                                
                                <td class="text-center align-middle">
                                    <a href="edit.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary rounded-pill me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <?php if ($total_kamar > 0): ?>
                                        <button class="btn btn-sm btn-danger rounded-pill" disabled 
                                                title="Tidak dapat dihapus karena ada <?php echo $total_kamar; ?> kamar yang menggunakan tipe ini.">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    <?php else: ?>
                                        <a href="hapus.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger rounded-pill" 
                                           onclick="return confirm('Hapus Tipe Kamar <?php echo htmlspecialchars($r['nama_tipe']); ?>?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($q->num_rows === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">
                                        Belum ada Tipe Kamar yang terdaftar.
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