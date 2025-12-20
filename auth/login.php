<?php
require_once '../config/koneksi.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (Logika PHP tetap sama)
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $u = $res->fetch_assoc();
        if (password_verify($password, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['user_name'] = $u['full_name'];
            $_SESSION['user_role'] = $u['role'];
            header('Location: ../dashboard.php');
            exit;
        } else {
            $error = 'Password salah';
        }
    } else {
        $error = 'User tidak ditemukan';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login Sistem Hotel</title>

    <style>
        body {
            /* Background yang menarik */
            background: linear-gradient(135deg, #74EBD5 0%, #9FACE6 100%);
            height: 100vh;
            /* Flexbox, tapi kita akan gunakan kelas Bootstrap di HTML */
        }
        .login-card {
            animation: fadeIn 1s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="card login-card shadow-lg p-4 p-md-5 bg-white rounded-4" style="max-width:420px; width: 100%;">
        
        <h3 class="mb-4 text-center pb-2 border-bottom border-3 border-info fw-bold">
            Hotel hary
        </h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input name="username" id="username" class="form-control rounded-3" required
                       placeholder="Masukkan Username Anda">
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input name="password" id="password" type="password" class="form-control rounded-3"
                       required placeholder="Masukkan Password Anda">
            </div>
            
            <button type="submit" class="btn btn-info btn-lg fw-bold text-white w-100 mb-3 rounded-3">
                Login
            </button>
            
            
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>