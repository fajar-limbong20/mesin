<?php
$page_title = 'Edit Data Historis';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM dataset WHERE id_data = ?");
$stmt->execute([$id]);
$d = $stmt->fetch();
if (!$d) { set_flash('danger','Data tidak ditemukan'); redirect('pages/dataset/index.php'); }

$mesinList = $pdo->query("SELECT * FROM mesin")->fetchAll();
$jenisList = ['Paper Jam','Overheat','Getaran Berlebih','Kerusakan Mekanik','Gangguan Listrik','Maintenance Rutin','Tidak Ada'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE dataset SET 
        id_mesin=?, tanggal=?, waktu_downtime=?, jam_operasi=?, suhu_mesin=?, 
        getaran=?, konsumsi_listrik=?, umur_komponen=?, downtime=?, 
        jenis_gangguan=?, frekuensi_kerusakan=?, status_downtime=? 
        WHERE id_data=?");
    $stmt->execute([
        (int)$_POST['id_mesin'],
        $_POST['tanggal'],
        !empty($_POST['waktu_downtime']) ? $_POST['waktu_downtime'] : null,
        (float)$_POST['jam_operasi'],
        (float)$_POST['suhu_mesin'],
        (float)$_POST['getaran'],
        (float)$_POST['konsumsi_listrik'],
        (int)$_POST['umur_komponen'],
        (int)$_POST['downtime'],
        $_POST['jenis_gangguan'],
        (int)$_POST['frekuensi_kerusakan'],
        $_POST['status_downtime'],
        $id
    ]);
    set_flash('success', 'Data berhasil diupdate.');
    redirect('pages/dataset/index.php');
}
?>
<div class="card">
    <div class="card-header bg-white"><strong>Edit Data Historis</strong></div>
    <div class="card-body">
        <form method="POST">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="small">Mesin</label>
                    <select name="id_mesin" class="form-select form-select-sm" required>
                        <?php foreach ($mesinList as $m): ?>
                            <option value="<?= $m['id_mesin'] ?>" <?= $d['id_mesin']==$m['id_mesin']?'selected':'' ?>>
                                <?= e($m['nama_mesin']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= e($d['tanggal']) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="small">Waktu</label>
                    <input type="time" name="waktu_downtime" class="form-control form-control-sm" value="<?= e($d['waktu_downtime']) ?>">
                </div>
                <div class="col-md-1">
                    <label class="small">Jam</label>
                    <input type="number" step="0.1" name="jam_operasi" class="form-control form-control-sm" value="<?= e($d['jam_operasi']) ?>" required>
                </div>
                <div class="col-md-1">
                    <label class="small">Suhu</label>
                    <input type="number" step="0.1" name="suhu_mesin" class="form-control form-control-sm" value="<?= e($d['suhu_mesin']) ?>" required>
                </div>
                <div class="col-md-1">
                    <label class="small">Getar</label>
                    <input type="number" step="0.1" name="getaran" class="form-control form-control-sm" value="<?= e($d['getaran']) ?>" required>
                </div>
                <div class="col-md-1">
                    <label class="small">kWh</label>
                    <input type="number" step="0.1" name="konsumsi_listrik" class="form-control form-control-sm" value="<?= e($d['konsumsi_listrik']) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="small">Umur (hari)</label>
                    <input type="number" name="umur_komponen" class="form-control form-control-sm" value="<?= e($d['umur_komponen']) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="small">Durasi (mnt)</label>
                    <input type="number" name="downtime" class="form-control form-control-sm" value="<?= e($d['downtime']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="small">Jenis Gangguan</label>
                    <select name="jenis_gangguan" class="form-select form-select-sm">
                        <?php foreach ($jenisList as $j): ?>
                            <option value="<?= $j ?>" <?= $d['jenis_gangguan']==$j?'selected':'' ?>><?= $j ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">Frekuensi</label>
                    <input type="number" name="frekuensi_kerusakan" class="form-control form-control-sm" value="<?= e($d['frekuensi_kerusakan']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="small">Status</label>
                    <select name="status_downtime" class="form-select form-select-sm" required>
                        <option value="Tidak" <?= $d['status_downtime']=='Tidak'?'selected':'' ?>>Tidak</option>
                        <option value="Ya"    <?= $d['status_downtime']=='Ya'?'selected':'' ?>>Ya</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>