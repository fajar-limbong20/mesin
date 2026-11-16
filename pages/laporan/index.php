<?php
$page_title = 'Laporan';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

// Filter
$dari   = $_GET['dari']   ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Data prediksi
$stmt = $pdo->prepare("
    SELECT p.*, m.nama_mesin
    FROM prediksi p
    JOIN mesin m ON p.id_mesin = m.id_mesin
    WHERE DATE(p.tanggal_prediksi) BETWEEN ? AND ?
    ORDER BY p.tanggal_prediksi DESC
");
$stmt->execute([$dari, $sampai]);
$prediksi = $stmt->fetchAll();

// Statistik
$totalPred   = count($prediksi);
$totalDownt  = 0;
foreach ($prediksi as $p) if ($p['hasil_prediksi'] == 'Ya') $totalDownt++;
$totalNormal = $totalPred - $totalDownt;

// Data downtime historis
$stmt2 = $pdo->prepare("
    SELECT d.*, m.nama_mesin
    FROM dataset d
    JOIN mesin m ON d.id_mesin = m.id_mesin
    WHERE d.tanggal BETWEEN ? AND ?
    AND d.status_downtime = 'Ya'
    ORDER BY d.tanggal DESC
");
$stmt2->execute([$dari, $sampai]);
$downtime = $stmt2->fetchAll();
?>
<div class="card mb-3">
    <div class="card-header bg-white"><strong>🔍 Filter Laporan</strong></div>
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <label class="small">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= e($dari) ?>">
            </div>
            <div class="col-md-3">
                <label class="small">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= e($sampai) ?>">
            </div>
            <div class="col-md-3">
                <label class="small">&nbsp;</label>
                <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Tampilkan</button>
            </div>
            <div class="col-md-3">
                <label class="small">&nbsp;</label>
                <button onclick="window.print()" type="button" class="btn btn-success w-100">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card stat-card" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
            <div class="card-body text-white">
                <div class="small opacity-75">Total Prediksi</div>
                <h2 class="mb-0"><?= $totalPred ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
            <div class="card-body text-white">
                <div class="small opacity-75">Prediksi Downtime</div>
                <h2 class="mb-0"><?= $totalDownt ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="card-body text-white">
                <div class="small opacity-75">Prediksi Normal</div>
                <h2 class="mb-0"><?= $totalNormal ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-white"><strong>📜 Riwayat Prediksi (<?= $totalPred ?>)</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm datatable">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th><th>Mesin</th><th>Gangguan</th>
                        <th>Suhu</th><th>Getar</th><th>Hasil</th><th>Prob</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prediksi as $p): ?>
                    <tr>
                        <td><small><?= date('d/m/y H:i', strtotime($p['tanggal_prediksi'])) ?></small></td>
                        <td><small><?= e($p['nama_mesin']) ?></small></td>
                        <td><small><?= e($p['jenis_gangguan']) ?></small></td>
                        <td><?= $p['suhu_mesin'] ?></td>
                        <td><?= $p['getaran'] ?></td>
                        <td>
                            <span class="badge bg-<?= $p['hasil_prediksi']=='Ya'?'danger':'success' ?>">
                                <?= $p['hasil_prediksi'] ?>
                            </span>
                        </td>
                        <td><?= $p['probabilitas'] ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white"><strong>⚠ Data Downtime Historis (<?= count($downtime) ?>)</strong></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm datatable">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th><th>Mesin</th><th>Jenis Gangguan</th>
                        <th>Durasi (mnt)</th><th>Frek.</th><th>Suhu</th><th>Getar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($downtime as $d): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($d['tanggal'])) ?></td>
                        <td><small><?= e($d['nama_mesin']) ?></small></td>
                        <td><span class="badge bg-info"><?= e($d['jenis_gangguan']) ?></span></td>
                        <td><?= $d['downtime'] ?></td>
                        <td><?= $d['frekuensi_kerusakan'] ?>x</td>
                        <td><?= $d['suhu_mesin'] ?></td>
                        <td><?= $d['getaran'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>