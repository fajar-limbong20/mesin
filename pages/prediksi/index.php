<?php
$page_title = 'Prediksi Downtime';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$mesinList  = $pdo->query("SELECT * FROM mesin")->fetchAll();
$modelAktif = $pdo->query("SELECT * FROM model_rf WHERE status='aktif' ORDER BY akurasi DESC LIMIT 1")->fetch();
$flash      = get_flash();

$riwayat = $pdo->query("
    SELECT p.*, m.nama_mesin 
    FROM prediksi p 
    JOIN mesin m ON p.id_mesin = m.id_mesin 
    ORDER BY p.tanggal_prediksi DESC LIMIT 50
")->fetchAll();

$jenisList = ['Tidak Ada','Paper Jam','Overheat','Getaran Berlebih','Kerusakan Mekanik','Gangguan Listrik','Maintenance Rutin'];
?>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div><?php endif; ?>

<?php if (!$modelAktif): ?>
    <div class="alert alert-warning">
        ⚠ Belum ada model aktif. <a href="<?= BASE_URL ?>pages/training/index.php">Training RF terlebih dahulu</a>.
    </div>
<?php else: ?>
<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>🔮 Input Parameter Mesin</strong>
                <span class="badge bg-success">Akurasi <?= $modelAktif['akurasi'] ?>%</span>
            </div>
            <div class="card-body">
                <form action="proses.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mesin</label>
                        <select name="id_mesin" class="form-select" required>
                            <?php foreach ($mesinList as $m): ?>
                                <option value="<?= $m['id_mesin'] ?>"><?= e($m['nama_mesin']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Jam Operasi</label>
                            <input type="number" step="0.1" name="jam_operasi" class="form-control form-control-sm" value="8" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Suhu (°C)</label>
                            <input type="number" step="0.1" name="suhu_mesin" class="form-control form-control-sm" value="45" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Getaran (mm/s)</label>
                            <input type="number" step="0.1" name="getaran" class="form-control form-control-sm" value="2" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Konsumsi (kWh)</label>
                            <input type="number" step="0.1" name="konsumsi_listrik" class="form-control form-control-sm" value="12" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Umur Komponen (hari)</label>
                            <input type="number" name="umur_komponen" class="form-control form-control-sm" value="30" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Durasi Downtime (mnt)</label>
                            <input type="number" name="downtime" class="form-control form-control-sm" value="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Frekuensi Kerusakan</label>
                            <input type="number" name="frekuensi_kerusakan" class="form-control form-control-sm" value="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Jenis Gangguan</label>
                            <select name="jenis_gangguan" class="form-select form-select-sm">
                                <?php foreach ($jenisList as $j): ?>
                                    <option value="<?= $j ?>"><?= $j ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-magic"></i> Prediksi Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white"><strong>📜 Riwayat Prediksi</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm datatable">
                        <thead class="table-light">
    <tr>
        <th>Waktu</th>         <!-- 1 -->
        <th>Mesin</th>         <!-- 2 -->
        <th>Gangguan</th>      <!-- 3 -->
        <th>Hasil</th>         <!-- 4 -->
        <th>Probabilitas</th>  <!-- 5 -->
        <th>Aksi</th>          <!-- 6 -->
    </tr>
</thead>
<tbody>
    <?php foreach ($riwayat as $r): ?>
    <tr>
        <td><small><?= date('d/m/y H:i', strtotime($r['tanggal_prediksi'])) ?></small></td>
        <td><small><?= e($r['nama_mesin']) ?></small></td>
        <td><small><span class="badge bg-info"><?= e($r['jenis_gangguan']) ?></span></small></td>
        <td>
            <span class="badge bg-<?= $r['hasil_prediksi']=='Ya'?'danger':'success' ?>">
                <?= $r['hasil_prediksi']=='Ya'?'⚠ Downtime':'✓ Normal' ?>
            </span>
        </td>
        <td><?= $r['probabilitas'] ?>%</td>
        <td>
            <a href="hasil.php?id=<?= $r['id_prediksi'] ?>" class="btn btn-sm btn-info">
                <i class="bi bi-eye"></i>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>