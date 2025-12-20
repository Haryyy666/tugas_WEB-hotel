<?php
require_once '../config/koneksi.php';
require_once '../inc/auth_check.php';
// Memastikan user sudah login dan memiliki role 'admin'
require_login();
require_role(['admin']);

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Password akan di-hash
    $role = $_POST['role'];
    
    // Validasi sederhana
    if (empty($full_name) || empty($username) || empty($password)) {
        $error = "Nama Lengkap, Username, dan Password wajib diisi.";
    } elseif (!in_array($role, ['admin', 'resepsionis'])) {
        $error = "Role tidak valid.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Cek jika username sudah ada (Peningkatan Keamanan/UX)
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $check->bind_param('s', $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Username sudah digunakan. Mohon pilih username lain.";
        } else {
            // Insert ke database
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $full_name, $username, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                // Gunakan parameter GET untuk pesan sukses
                header('Location: register.php?msg=created'); 
                exit;
            } else {
                // Tampilkan error database
                $error = "Gagal mendaftarkan user: " . $stmt->error;
            }
        }
    }
}

// Ambil pesan sukses dari URL
if (isset($_GET['msg']) && $_GET['msg'] === 'created') {
    $message = "Pengguna baru berhasil didaftarkan!";
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <title>Register User</title>
</head>

<body class="bg-light p-3">
    
    <div class="container py-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width:720px; margin: 0 auto;">
            
            <div class="card-header bg-success text-white rounded-top-4 p-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i> Register Pengguna Baru (Admin)
                </h3>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal!</strong> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Sukses!</strong> <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="full_name" class="form-label fw-semibold">Nama Lengkap</label>
                            <input name="full_name" id="full_name" class="form-control form-control-lg rounded-3" required
                                   placeholder="Nama lengkap user">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input name="username" id="username" class="form-control form-control-lg rounded-3" required
                                   placeholder="Username untuk login">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label fw-semibold">Email (Opsional)</label>
                            <input name="email" id="email" type="email" class="form-control form-control-lg rounded-3"
                                   placeholder="Alamat email user">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input name="password" id="password" type="password" class="form-control form-control-lg rounded-3" 
                                   required placeholder="Password sementara">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="role" class="form-label fw-semibold">Role Pengguna</label>
                        <select name="role" id="role" class="form-select form-select-lg rounded-3">
                            <option value="admin">Admin</option>
                            <option value="resepsionis" selected>Resepsionis</option>
                        </select>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-semibold rounded-pill shadow">
                            <i class="bi bi-person-plus me-2"></i> Register
                        </button>
                        <a href="../dashboard.php" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>