<?php
$page_title = 'Training Random Forest';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$totalData    = $pdo->query("SELECT COUNT(*) FROM dataset")->fetchColumn();
$totalYa      = $pdo->query("SELECT COUNT(*) FROM dataset WHERE status_downtime='Ya'")->fetchColumn();
$totalTidak   = $pdo->query("SELECT COUNT(*) FROM dataset WHERE status_downtime='Tidak'")->fetchColumn();
$models       = $pdo->query("SELECT * FROM model_rf ORDER BY tanggal_training DESC")->fetchAll();
$flash        = get_flash();
?>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div><?php endif; ?>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white"><strong>⚙ Parameter Random Forest</strong></div>
            <div class="card-body">
                <div class="alert alert-light border small mb-3">
                    <strong>Statistik Data:</strong>
                    <div class="row text-center mt-2">
                        <div class="col-4">
                            <div class="text-muted small">Total</div>
                            <strong><?= $totalData ?></strong>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Ya</div>
                            <strong class="text-danger"><?= $totalYa ?></strong>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Tidak</div>
                            <strong class="text-success"><?= $totalTidak ?></strong>
                        </div>
                    </div>
                </div>

                <?php if ($totalData < 10): ?>
                    <div class="alert alert-warning small">
                        ⚠ Minimal 10 baris data historis direkomendasikan untuk training yang baik.
                    </div>
                <?php endif; ?>

                <form action="proses.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Model</label>
                        <input name="nama_model" class="form-control form-control-sm" 
                               value="RF Canon C10000 - <?= date('d/m/Y H:i') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Pohon (n_estimators)</label>
                        <input type="number" name="n_trees" class="form-control form-control-sm" 
                               value="10" min="1" max="100" required>
                        <small class="text-muted">Rekomendasi: 10 - 100</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Depth</label>
                        <input type="number" name="max_depth" class="form-control form-control-sm" 
                               value="5" min="1" max="20" required>
                        <small class="text-muted">Kedalaman maksimal setiap pohon</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Min Samples Split</label>
                        <input type="number" name="min_samples" class="form-control form-control-sm" 
                               value="2" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rasio Split Training:Testing</label>
                        <select name="rasio" class="form-select form-select-sm">
                            <option value="0.80">80 : 20 (Rekomendasi)</option>
                            <option value="0.75">75 : 25</option>
                            <option value="0.70">70 : 30</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" <?= $totalData < 5 ? 'disabled' : '' ?>>
                        <i class="bi bi-play-fill"></i> Mulai Training
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white"><strong>📚 Riwayat Model</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-sm datatable">
                    <thead class="table-light">
    <tr>
        <th>Model</th>       <!-- 1 -->
        <th>Pohon</th>       <!-- 2 -->
        <th>Akurasi</th>     <!-- 3 -->
        <th>F1</th>          <!-- 4 -->
        <th>Tgl</th>         <!-- 5 -->
        <th>Status</th>      <!-- 6 -->
        <th>Aksi</th>        <!-- 7 -->
    </tr>
</thead>
<tbody>
    <?php foreach ($models as $m): ?>
    <tr>
        <td><small><?= e($m['nama_model']) ?></small></td>
        <td><?= $m['n_trees'] ?></td>
        <td><span class="badge bg-..."><?= $m['akurasi'] ?>%</span></td>
        <td><?= $m['f1_score'] ?>%</td>
        <td><small><?= date('d/m/y H:i', strtotime($m['tanggal_training'])) ?></small></td>
        <td>
            <?= $m['status']=='aktif'
                ? '<span class="badge bg-primary">Aktif</span>'
                : '<span class="badge bg-secondary">-</span>' ?>
        </td>
        <td>
            <a href="detail.php?id=<?= $m['id_model'] ?>" class="btn btn-sm btn-info">
                <i class="bi bi-eye"></i>
            </a>
            <a href="hapus.php?id=<?= $m['id_model'] ?>" class="btn btn-sm btn-danger" 
               onclick="return confirm('Hapus model ini?')">
                <i class="bi bi-trash"></i>
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
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>