<?php
$page_title = 'Edit Mesin';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM mesin WHERE id_mesin = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();
if (!$m) { set_flash('danger','Data tidak ditemukan'); redirect('pages/mesin/index.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE mesin SET kode_mesin=?, nama_mesin=?, tahun_instalasi=?, status=? WHERE id_mesin=?");
    $stmt->execute([$_POST['kode_mesin'], $_POST['nama_mesin'], $_POST['tahun_instalasi'], $_POST['status'], $id]);
    set_flash('success', 'Data berhasil diupdate.');
    redirect('pages/mesin/index.php');
}
?>
<div class="card">
    <div class="card-header bg-white"><strong>Edit Mesin</strong></div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode Mesin</label>
                    <input name="kode_mesin" class="form-control" value="<?= e($m['kode_mesin']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Mesin</label>
                    <input name="nama_mesin" class="form-control" value="<?= e($m['nama_mesin']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tahun Instalasi</label>
                    <input type="number" name="tahun_instalasi" class="form-control" value="<?= e($m['tahun_instalasi']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['aktif','maintenance','rusak'] as $s): ?>
                            <option value="<?= $s ?>" <?= $m['status']==$s?'selected':'' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>