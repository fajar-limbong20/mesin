<?php
require_once __DIR__ . '/../../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    try {
        $pdo->prepare("DELETE FROM mesin WHERE id_mesin = ?")->execute([$id]);
        set_flash('success', 'Data mesin berhasil dihapus.');
    } catch (Exception $e) {
        set_flash('danger', 'Gagal hapus: mesin masih dipakai di dataset.');
    }
}
redirect('pages/mesin/index.php');