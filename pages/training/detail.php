<?php
$page_title = 'Detail Model Random Forest';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM model_rf WHERE id_model = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();
if (!$m) { set_flash('danger','Model tidak ditemukan'); redirect('pages/training/index.php'); }

$stmt = $pdo->prepare("SELECT * FROM evaluasi WHERE id_model = ?");
$stmt->execute([$id]);
$ev = $stmt->fetch();

// Data training/testing
$training = $pdo->query("SELECT COUNT(*) FROM preprocessing WHERE tipe_data='training'")->fetchColumn();
$testing  = $pdo->query("SELECT COUNT(*) FROM preprocessing WHERE tipe_data='testing'")->fetchColumn();
?>
<?php $flash = get_flash(); if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white"><strong>📋 Informasi Model</strong></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>Nama</th><td><?= e($m['nama_model']) ?></td></tr>
                    <tr><th>Jumlah Pohon</th><td><?= $m['n_trees'] ?></td></tr>
                    <tr><th>Max Depth</th><td><?= $m['max_depth'] ?></td></tr>
                    <tr><th>Min Samples</th><td><?= $m['min_samples'] ?></td></tr>
                    <tr><th>Rasio Split</th><td><?= ($m['rasio_split']*100) ?>% : <?= (100-$m['rasio_split']*100) ?>%</td></tr>
                    <tr><th>Data Training</th><td><?= $training ?> baris</td></tr>
                    <tr><th>Data Testing</th><td><?= $testing ?> baris</td></tr>
                    <tr><th>Tanggal</th><td><?= $m['tanggal_training'] ?></td></tr>
                    <tr><th>Status</th><td>
                        <?= $m['status']=='aktif'
                            ? '<span class="badge bg-success">Aktif</span>'
                            : '<span class="badge bg-secondary">Nonaktif</span>' ?>
                    </td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white"><strong>📊 Evaluasi Performa</strong></div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-3">
                        <div class="border rounded p-3">
                            <div class="small text-muted">Akurasi</div>
                            <h3 class="text-primary mb-0"><?= $m['akurasi'] ?>%</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-3">
                            <div class="small text-muted">Precision</div>
                            <h3 class="text-info mb-0"><?= $m['precision_score'] ?>%</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-3">
                            <div class="small text-muted">Recall</div>
                            <h3 class="text-warning mb-0"><?= $m['recall_score'] ?>%</h3>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-3">
                            <div class="small text-muted">F1-Score</div>
                            <h3 class="text-success mb-0"><?= $m['f1_score'] ?>%</h3>
                        </div>
                    </div>
                </div>

                <?php if ($ev): ?>
                <h6>Confusion Matrix</h6>
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th colspan="2" rowspan="2"></th>
                            <th colspan="2">Aktual</th>
                        </tr>
                        <tr>
                            <th>Ya</th><th>Tidak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th rowspan="2" class="table-light">Prediksi</th>
                            <th class="table-light">Ya</th>
                            <td class="bg-success text-white"><strong><?= $ev['tp'] ?></strong><br><small>TP</small></td>
                            <td class="bg-danger text-white"><strong><?= $ev['fp'] ?></strong><br><small>FP</small></td>
                        </tr>
                        <tr>
                            <th class="table-light">Tidak</th>
                            <td class="bg-danger text-white"><strong><?= $ev['fn'] ?></strong><br><small>FN</small></td>
                            <td class="bg-success text-white"><strong><?= $ev['tn'] ?></strong><br><small>TN</small></td>
                        </tr>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    <a href="<?= BASE_URL ?>pages/prediksi/index.php" class="btn btn-primary">
        <i class="bi bi-magic"></i> Gunakan untuk Prediksi
    </a>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>