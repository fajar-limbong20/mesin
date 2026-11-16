<?php
require_once __DIR__ . '/../../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // Cek apakah model aktif
    $cek = $pdo->prepare("SELECT status FROM model_rf WHERE id_model = ?");
    $cek->execute([$id]);
    $model = $cek->fetch();

    if ($model && $model['status'] === 'aktif') {
        // Aktifkan model lain (terbaik setelah ini)
        $next = $pdo->query("SELECT id_model FROM model_rf WHERE id_model != $id ORDER BY akurasi DESC LIMIT 1")->fetch();
        if ($next) {
            $pdo->prepare("UPDATE model_rf SET status='aktif' WHERE id_model = ?")->execute([$next['id_model']]);
        }
    }

    $pdo->prepare("DELETE FROM model_rf WHERE id_model = ?")->execute([$id]);
    set_flash('success', 'Model berhasil dihapus.');
}
redirect('pages/training/index.php');