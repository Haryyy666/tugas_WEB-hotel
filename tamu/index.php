<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
require_login();

// Query untuk mengambil semua data tamu termasuk field email yang baru
$q = $conn->query("SELECT * FROM tamu ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <title>Daftar Tamu</title>
</head>

<body class="bg-light p-3">
    <div class="container-fluid py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="h3 fw-bold text-dark">
                <i class="bi bi-person-badge-fill me-2 text-primary"></i> Daftar Tamu
            </h1>
            <a href="tambah.php" class="btn btn-success btn-lg rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Tambah Tamu
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 small">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col">Nama Tamu</th>
                                <th scope="col">No HP</th>
                                <th scope="col">NIK</th>
                                <th scope="col">Email</th> <th scope="col">Alamat</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; while($r = $q->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center align-middle"><?php echo $i++; ?></td>
                                <td class="fw-semibold align-middle"><?php echo htmlspecialchars($r['nama']); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($r['no_hp']); ?></td>
                                <td class="align-middle"><code><?php echo htmlspecialchars($r['nik']); ?></code></td>
                                
                                <td class="align-middle text-primary">
                                    <?php echo htmlspecialchars($r['email'] ?: '-'); ?>
                                </td>

                                <td class="text-muted small align-middle">
                                    <?php 
                                    echo htmlspecialchars(substr($r['alamat'], 0, 30)); 
                                    echo (strlen($r['alamat']) > 30) ? '...' : ''; 
                                    ?>
                                </td>
                                
                                <td class="text-center align-middle">
                                    <a href="edit.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-primary rounded-pill me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger rounded-pill" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data tamu: <?php echo htmlspecialchars($r['nama']); ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if ($q->num_rows === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted p-4">
                                        Belum ada data Tamu yang terdaftar.
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