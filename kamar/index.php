<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// Query diperbarui untuk mengambil kolom lantai dan deskripsi
$q = $conn->query("
    SELECT 
        k.id, 
        k.nomor_kamar, 
        k.lantai,
        k.status,
        k.deskripsi,
        t.nama_tipe 
    FROM kamar k
    JOIN tipe_kamar t ON k.id_tipe = t.id
    ORDER BY k.nomor_kamar ASC
");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <title>Daftar Kamar Hotel</title>
</head>

<body class="bg-light p-3">
    <div class="container-fluid py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h3 fw-bold text-dark">
                <i class="bi bi-door-open-fill me-2 text-primary"></i> Daftar Kamar
            </h1>
            <a href="tambah.php" class="btn btn-success btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Tambah Kamar
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col">Nomor Kamar</th>
                                <th scope="col">Lantai</th> <th scope="col">Tipe Kamar</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col">Keterangan</th> <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($r = $q->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo $i++; ?></td>
                                <td class="fw-bold align-middle text-dark"><?php echo htmlspecialchars($r['nomor_kamar']); ?></td>
                                
                                <td class="align-middle text-center">
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($r['lantai'] ?: '-'); ?></span>
                                </td>

                                <td class="align-middle">
                                    <span class="fw-semibold text-primary"><?php echo htmlspecialchars($r['nama_tipe']); ?></span>
                                </td>
                                
                                <td class="text-center align-middle">
                                    <?php 
                                    $status = htmlspecialchars($r['status']);
                                    $badge_class = 'bg-secondary';
                                    if ($status === 'tersedia') { // Sesuaikan kecil/besar dengan DB Anda
                                        $badge_class = 'bg-success';
                                    } elseif ($status === 'dipesan') {
                                        $badge_class = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?> p-2 px-3 fw-bold"><?php echo ucfirst($status); ?></span>
                                </td>

                                <td class="align-middle small text-muted">
                                    <?php echo htmlspecialchars($r['deskripsi'] ?: '-'); ?>
                                </td>
                                
                                <td class="text-center align-middle">
                                    <a href="edit.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary rounded-pill me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger rounded-pill" 
                                       onclick="return confirm('Hapus Kamar Nomor <?php echo htmlspecialchars($r['nomor_kamar']); ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($q->num_rows === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted p-4">
                                        Belum ada Kamar yang terdaftar.
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