<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    redirect('auth/login.php');
}

function current_user() {
    return [
        'id'    => $_SESSION['user_id']    ?? null,
        'nama'  => $_SESSION['user_nama']  ?? '',
        'level' => $_SESSION['user_level'] ?? ''
    ];
}

function require_admin() {
    if (($_SESSION['user_level'] ?? '') !== 'admin') {
        set_flash('danger', 'Akses ditolak. Hanya admin.');
        redirect('pages/dashboard.php');
    }
}