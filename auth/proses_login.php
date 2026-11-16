<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    set_flash('danger', 'Username dan password wajib diisi!');
    redirect('auth/login.php');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = MD5(?) LIMIT 1");
$stmt->execute([$username, $password]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['user_id']    = $user['id_user'];
    $_SESSION['user_nama']  = $user['nama'];
    $_SESSION['user_level'] = $user['level'];
    
    try {
        $pdo->prepare("INSERT INTO log_aktivitas (id_user, aktivitas) VALUES (?,?)")
            ->execute([$user['id_user'], 'Login ke sistem']);
    } catch (Exception $e) {
        // Abaikan error log — jangan block login
    }
    
    set_flash('success', 'Selamat datang, ' . $user['nama'] . '!');
    redirect('pages/dashboard.php');
} else {
    set_flash('danger', 'Username atau password salah!');
    redirect('auth/login.php');
}