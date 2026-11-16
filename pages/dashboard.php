<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$totalData     = $pdo->query("SELECT COUNT(*) FROM dataset")->fetchColumn();
$totalDowntime = $pdo->query("SELECT COUNT(*) FROM dataset WHERE status_downtime='Ya'")->fetchColumn();
$totalNormal   = $pdo->query("SELECT COUNT(*) FROM dataset WHERE status_downtime='Tidak'")->fetchColumn();
$totalMesin    = $pdo->query("SELECT COUNT(*) FROM mesin")->fetchColumn();

$modelAktif = $pdo->query("SELECT * FROM model_rf WHERE status='aktif' ORDER BY akurasi DESC LIMIT 1")->fetch();

// Tren per bulan
$perBulan = $pdo->query("
    SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
           SUM(CASE WHEN status_downtime='Ya' THEN 1 ELSE 0 END) AS ya,
           SUM(CASE WHEN status_downtime='Tidak' THEN 1 ELSE 0 END) AS tidak
    FROM dataset GROUP BY bulan ORDER BY bulan ASC
")->fetchAll();

// Distribusi jenis gangguan
$gangguan = $pdo->query("
    SELECT jenis_gangguan, COUNT(*) AS jml 
    FROM dataset WHERE jenis_gangguan != 'Tidak Ada'
    GROUP BY jenis_gangguan ORDER BY jml DESC
")->fetchAll();

$labels   = array_column($perBulan, 'bulan');
$dataYa   = array_column($perBulan, 'ya');
$dataTdk  = array_column($perBulan, 'tidak');
?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Total Data</div>
                        <h2 class="mb-0"><?= $totalData ?></h2>
                    </div>
                    <i class="bi bi-database" style="font-size:40px;opacity:.4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Downtime</div>
                        <h2 class="mb-0"><?= $totalDowntime ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size:40px;opacity:.4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Normal</div>
                        <h2 class="mb-0"><?= $totalNormal ?></h2>
                    </div>
                    <i class="bi bi-check-circle" style="font-size:40px;opacity:.4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small opacity-75">Total Mesin</div>
                        <h2 class="mb-0"><?= $totalMesin ?></h2>
                    </div>
                    <i class="bi bi-printer" style="font-size:40px;opacity:.4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white"><strong>📊 Tren Downtime per Bulan</strong></div>
            <div class="card-body">
                <?php if (empty($perBulan)): ?>
                    <p class="text-muted text-center my-5">Belum ada data</p>
                <?php else: ?>
                    <canvas id="chartBulan" height="100"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><strong>🤖 Model Aktif</strong></div>
            <div class="card-body">
                <?php if ($modelAktif): ?>
                    <h6 class="mb-3"><?= e($modelAktif['nama_model']) ?></h6>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Akurasi</span><strong><?= $modelAktif['akurasi'] ?>%</strong>
                        </div>
                        <div class="progress" style="height:8px">
                            <div class="progress-bar bg-success" style="width:<?= $modelAktif['akurasi'] ?>%"></div>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless small mb-0">
                        <tr><td>Precision</td><td class="text-end"><strong><?= $modelAktif['precision_score'] ?>%</strong></td></tr>
                        <tr><td>Recall</td><td class="text-end"><strong><?= $modelAktif['recall_score'] ?>%</strong></td></tr>
                        <tr><td>F1-Score</td><td class="text-end"><strong><?= $modelAktif['f1_score'] ?>%</strong></td></tr>
                        <tr><td>Jumlah Pohon</td><td class="text-end"><strong><?= $modelAktif['n_trees'] ?></strong></td></tr>
                        <tr><td>Max Depth</td><td class="text-end"><strong><?= $modelAktif['max_depth'] ?></strong></td></tr>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Belum ada model aktif.</p>
                    <a href="<?= BASE_URL ?>pages/training/index.php" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-play-fill"></i> Mulai Training
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($gangguan)): ?>
<div class="card mt-3">
    <div class="card-header bg-white"><strong>⚠ Distribusi Jenis Gangguan</strong></div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($gangguan as $g): ?>
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3 text-center">
                        <div class="small text-muted"><?= e($g['jenis_gangguan']) ?></div>
                        <h4 class="mb-0"><?= $g['jml'] ?>x</h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($perBulan)): ?>
<script>
new Chart(document.getElementById('chartBulan'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            { label: 'Downtime', data: <?= json_encode($dataYa) ?>, backgroundColor: '#ef4444' },
            { label: 'Normal',   data: <?= json_encode($dataTdk) ?>, backgroundColor: '#10b981' }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>