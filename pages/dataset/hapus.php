<?php
require_once __DIR__ . '/../../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo->prepare("DELETE FROM dataset WHERE id_data = ?")->execute([$id]);
    set_flash('success', 'Data berhasil dihapus.');
}
redirect('pages/dataset/index.php');