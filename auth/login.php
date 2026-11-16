<?php
require_once __DIR__ . '/../config/config.php';
if (isset($_SESSION['user_id'])) redirect('pages/dashboard.php');
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login | <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: linear-gradient(135deg, #1e293b, #0f172a); min-height: 100vh; display: flex; align-items: center; }
    .login-card { max-width: 420px; margin: auto; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
</style>
</head>
<body>
<div class="container">
    <div class="card login-card">
        <div class="card-body p-4">
            <div class="text-center mb-3">
                <div style="font-size:40px">🎯</div>
                <h4 class="mb-1">Random Forest</h4>
                <p class="text-muted small mb-0">Prediksi Downtime Mesin Canon C10000</p>
                <p class="text-muted small"><strong>CV Harco Nancy</strong></p>
            </div>
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div>
            <?php endif; ?>
            <form action="proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100 py-2">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>
            <p class="text-center small text-muted mt-3 mb-0">
                Default: <code>admin</code> / <code>admin123</code>
            </p>
        </div>
    </div>
</div>
</body>
</html>