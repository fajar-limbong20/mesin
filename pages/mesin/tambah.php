<?php
$page_title = 'Tambah Mesin';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO mesin (kode_mesin, nama_mesin, tahun_instalasi, status) VALUES (?,?,?,?)");
    $stmt->execute([$_POST['kode_mesin'], $_POST['nama_mesin'], $_POST['tahun_instalasi'], $_POST['status']]);
    set_flash('success', 'Mesin berhasil ditambahkan.');
    redirect('pages/mesin/index.php');
}
?>
<div class="card">
    <div class="card-header bg-white"><strong>Tambah Mesin Baru</strong></div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode Mesin</label>
                    <input name="kode_mesin" class="form-control" placeholder="CNC-003" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Mesin</label>
                    <input name="nama_mesin" class="form-control" placeholder="Canon C10000 Unit 3" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tahun Instalasi</label>
                    <input type="number" name="tahun_instalasi" class="form-control" value="<?= date('Y') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="aktif">Aktif</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>