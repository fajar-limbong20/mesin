<?php
require_once __DIR__ . '/../config/config.php';

if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../config/database.php';
    
    try {
        // Verifikasi user masih ada di database sebelum insert log
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id_user = ?");
        $check->execute([$_SESSION['user_id']]);
        
        if ($check->fetchColumn() > 0) {
            $pdo->prepare("INSERT INTO log_aktivitas (id_user, aktivitas) VALUES (?,?)")
                ->execute([$_SESSION['user_id'], 'Logout dari sistem']);
        }
    } catch (Exception $e) {
        // Abaikan error — yang penting logout tetap jalan
    }
}

session_destroy();
redirect('auth/login.php');