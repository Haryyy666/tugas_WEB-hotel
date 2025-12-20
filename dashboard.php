<?php
require_once 'config/koneksi.php';
require_once 'inc/auth_check.php';
require_login();

// Pastikan koneksi tersedia dan query dieksekusi
$counts = [
    'tipe' => 0,
    'kamar' => 0,
    'tamu' => 0,
    'res' => 0
];
if (isset($conn)) {
    // Menggunakan JOIN dan sub-query dalam satu query untuk efisiensi
    $q = $conn->query("SELECT 
        (SELECT COUNT(*) FROM tipe_kamar) AS tipe, 
        (SELECT COUNT(*) FROM kamar) AS kamar, 
        (SELECT COUNT(*) FROM tamu) AS tamu, 
        (SELECT COUNT(*) FROM reservasi) AS res");
    
    if ($q && $q->num_rows > 0) {
        $counts = $q->fetch_assoc();
    }
}

// Data untuk kartu statistik (lebih mudah diatur)
$stats = [
    [
        'label' => 'Tipe Kamar', 
        'count' => $counts['tipe'], 
        'icon' => 'door-open-fill', // Ikon Bootstrap
        'color' => 'primary',
        'link' => 'tipe_kamar/index.php'
    ],
    [
        'label' => 'Kamar Tersedia', 
        'count' => $counts['kamar'], 
        'icon' => 'house-door-fill',
        'color' => 'success',
        'link' => 'kamar/index.php'
    ],
    [
        'label' => 'Data Tamu', 
        'count' => $counts['tamu'], 
        'icon' => 'people-fill',
        'color' => 'warning',
        'link' => 'tamu/index.php'
    ],
    [
        'label' => 'Total Reservasi', 
        'count' => $counts['res'], 
        'icon' => 'calendar-check-fill',
        'color' => 'danger',
        'link' => 'reservasi/index.php'
    ],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <title>Dashboard | Sistem Hotel</title>
    
    <style>
        body {
            background-color: #f8f9fa; /* Latar belakang abu-abu muda */
        }
        .info-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            cursor: pointer;
        }
        .info-card:hover {
            transform: translateY(-5px); /* Efek angkat saat hover */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .icon-square {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 8px;
            font-size: 1.5rem;
            color: white;
        }
        /* Custom color classes for icons */
        .bg-primary-light { background-color: #cfe2ff; color: #084298 !important; }
        .bg-success-light { background-color: #d1e7dd; color: #0f5132 !important; }
        .bg-warning-light { background-color: #fff3cd; color: #664d03 !important; }
        .bg-danger-light { background-color: #f8d7da; color: #842029 !important; }
    </style>
</head>

<body>
    <div class="container py-4">
        <header class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h1 class="h3 fw-bold text-dark">
                <i class="bi bi-grid-fill me-2 text-primary"></i> Dashboard Sistem Hotel
            </h1>
            <div class="text-secondary">
                Selamat Datang, 
                <span class="fw-bold text-dark me-2"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="auth/logout.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </header>

        <div class="row g-4 mb-5">
            <?php foreach ($stats as $stat): ?>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo htmlspecialchars($stat['link']); ?>" class="text-decoration-none">
                        <div class="card info-card shadow-sm p-3 bg-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted fw-semibold small mb-1"><?php echo $stat['label']; ?></div>
                                        <h4 class="card-title mb-0 fw-bolder text-<?php echo $stat['color']; ?>">
                                            <?php echo $stat['count']; ?>
                                        </h4>
                                    </div>
                                    <div class="icon-square bg-<?php echo $stat['color']; ?>-light text-<?php echo $stat['color']; ?>">
                                        <i class="bi bi-<?php echo $stat['icon']; ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <h4 class="mb-3 text-dark border-bottom pb-2">
            <i class="bi bi-journal-text me-2"></i> Navigasi Cepat
        </h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="tipe_kamar/index.php" class="btn btn-outline-primary btn-lg rounded-pill px-4 fw-semibold">
                <i class="bi bi-list-stars me-2"></i> Kelola Tipe Kamar
            </a>
            <a href="kamar/index.php" class="btn btn-outline-success btn-lg rounded-pill px-4 fw-semibold">
                <i class="bi bi-house-fill me-2"></i> Kelola Kamar
            </a>
            <a href="tamu/index.php" class="btn btn-outline-info btn-lg rounded-pill px-4 fw-semibold">
                <i class="bi bi-person-rolodex me-2"></i> Data Tamu
            </a>
            <a href="reservasi/index.php" class="btn btn-outline-warning btn-lg rounded-pill px-4 fw-semibold">
                <i class="bi bi-calendar-range-fill me-2"></i> Kelola Reservasi
            </a>
            
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="auth/register.php" class="btn btn-success btn-lg rounded-pill px-4 fw-semibold ms-md-auto">
                    <i class="bi bi-person-plus-fill me-2"></i> Register User Baru
                </a>
            <?php endif; ?>
        </div>
        
        <footer class="mt-5 pt-3 border-top text-center text-muted small">
            &copy; <?php echo date('Y'); ?> Sistem Manajemen Hotel. Hak Cipta Dilindungi.
        </footer>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>