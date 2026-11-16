<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0 && $id != $_SESSION['user_id']) {
    $pdo->prepare("DELETE FROM users WHERE id_user = ?")->execute([$id]);
    set_flash('success', 'User berhasil dihapus.');
} else {
    set_flash('danger', 'Tidak bisa menghapus akun sendiri.');
}
redirect('pages/user/index.php');