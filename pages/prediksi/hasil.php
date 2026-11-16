<?php
$page_title = 'Hasil Prediksi & Perhitungan Random Forest';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT p.*, m.nama_mesin, mr.nama_model, mr.akurasi, mr.n_trees, mr.max_depth
    FROM prediksi p
    JOIN mesin m ON p.id_mesin = m.id_mesin
    JOIN model_rf mr ON p.id_model = mr.id_model
    WHERE p.id_prediksi = ?
");
$stmt->execute([$id]);
$r = $stmt->fetch();
if (!$r) { echo "Data tidak ditemukan"; exit; }

// Ambil trace dari session
$sessionKey = 'pred_trace_' . $id;
$traceData  = $_SESSION[$sessionKey] ?? null;
$trace      = $traceData['trace'] ?? null;
$rawInput   = $traceData['raw_post'] ?? null;
?>
<style>
.tree-card {
    border-left: 3px solid #3b82f6;
    background: #f8fafc;
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 6px;
}
.tree-card.vote-ya    { border-left-color: #ef4444; background: #fef2f2; }
.tree-card.vote-tidak { border-left-color: #10b981; background: #f0fdf4; }
.path-step {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    padding: 4px 8px;
    margin: 2px 0;
    background: #fff;
    border-left: 2px solid #cbd5e1;
}
.path-step.kiri  { border-left-color: #3b82f6; }
.path-step.kanan { border-left-color: #f59e0b; }
.vote-box {
    display: inline-block;
    padding: 6px 12px;
    margin: 3px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
}
.progress-tall { height: 25px; }
.formula-box {
    background: #f1f5f9;
    padding: 12px 16px;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    margin-bottom: 10px;
}
</style>

<div class="row justify-content-center">
    <div class="col-md-11">

        <!-- ============================= -->
        <!-- 1. HASIL AKHIR -->
        <!-- ============================= -->
        <div class="card mb-3">
            <div class="card-header bg-white"><strong>🎯 Kesimpulan Prediksi</strong></div>
            <div class="card-body">
                <?php if ($r['hasil_prediksi'] == 'Ya'): ?>
                    <div class="text-center py-4 rounded" style="background:linear-gradient(135deg,#fee2e2,#fecaca)">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:60px"></i>
                        <h2 class="text-danger mt-2 mb-0">⚠ POTENSI DOWNTIME</h2>
                        <h1 class="text-danger"><?= $r['probabilitas'] ?>%</h1>
                        <p class="text-muted small">Probabilitas downtime terdeteksi dari <?= $r['n_trees'] ?> pohon</p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 rounded" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0)">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:60px"></i>
                        <h2 class="text-success mt-2 mb-0">✓ KONDISI NORMAL</h2>
                        <h1 class="text-success"><?= $r['probabilitas'] ?>%</h1>
                        <p class="text-muted small">Probabilitas mesin beroperasi normal dari <?= $r['n_trees'] ?> pohon</p>
                    </div>
                <?php endif; ?>

                <div class="alert alert-<?= $r['hasil_prediksi']=='Ya'?'warning':'info' ?> mt-3">
                    <strong>💡 Rekomendasi:</strong><br>
                    <?= nl2br(e($r['rekomendasi'])) ?>
                </div>
            </div>
        </div>

        <!-- ============================= -->
        <!-- 2. INFO MODEL -->
        <!-- ============================= -->
        <div class="card mb-3">
            <div class="card-header bg-white"><strong>🤖 Informasi Model</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="30%">Nama Model</th><td><?= e($r['nama_model']) ?></td></tr>
                    <tr><th>Akurasi Model</th><td><span class="badge bg-success"><?= $r['akurasi'] ?>%</span></td></tr>
                    <tr><th>Jumlah Pohon</th><td><?= $r['n_trees'] ?> pohon</td></tr>
                    <tr><th>Max Depth</th><td><?= $r['max_depth'] ?></td></tr>
                    <tr><th>Waktu Prediksi</th><td><?= $r['tanggal_prediksi'] ?></td></tr>
                    <tr><th>Mesin</th><td><?= e($r['nama_mesin']) ?></td></tr>
                </table>
            </div>
        </div>

        <!-- ============================= -->
        <!-- 3. TAHAP 1 — INPUT USER -->
        <!-- ============================= -->
        <?php if ($rawInput): ?>
        <div class="card mb-3">
            <div class="card-header bg-white">
                <strong>📥 TAHAP 1 — Input dari User</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">Data mentah yang diinputkan user sebelum diproses:</p>
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr><th>Parameter</th><th>Nilai</th><th>Tipe</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Jam Operasi</td><td><?= $rawInput['jam_operasi'] ?></td><td>Numerik</td></tr>
                        <tr><td>Suhu Mesin</td><td><?= $rawInput['suhu_mesin'] ?> °C</td><td>Numerik</td></tr>
                        <tr><td>Getaran</td><td><?= $rawInput['getaran'] ?> mm/s</td><td>Numerik</td></tr>
                        <tr><td>Konsumsi Listrik</td><td><?= $rawInput['konsumsi_listrik'] ?> kWh</td><td>Numerik</td></tr>
                        <tr><td>Umur Komponen</td><td><?= $rawInput['umur_komponen'] ?> hari</td><td>Numerik</td></tr>
                        <tr><td>Durasi Downtime</td><td><?= $rawInput['downtime'] ?> menit</td><td>Numerik</td></tr>
                        <tr><td>Frekuensi Kerusakan</td><td><?= $rawInput['frekuensi_kerusakan'] ?>x</td><td>Numerik</td></tr>
                        <tr><td>Jenis Gangguan</td><td><span class="badge bg-info"><?= e($rawInput['jenis_gangguan']) ?></span></td><td>Kategorikal</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============================= -->
        <!-- 4. TAHAP 2 — ENCODING -->
        <!-- ============================= -->
        <?php if ($trace): ?>
        <div class="card mb-3">
            <div class="card-header bg-white">
                <strong>🔄 TAHAP 2 — Transformasi Fitur (One-Hot Encoding)</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Variabel <code>jenis_gangguan</code> yang bersifat <strong>kategorikal</strong> diubah menjadi 
                    <strong>7 kolom biner (0/1)</strong> agar bisa diproses Random Forest.
                </p>

                <div class="formula-box">
                    jenis_gangguan = "<?= e($rawInput['jenis_gangguan'] ?? '-') ?>" 
                    → [PaperJam, Overheat, Getaran, Mekanik, Listrik, Maintenance, TidakAda]
                </div>

                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr><th>Fitur Final (X)</th><th>Nilai</th><th>Keterangan</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trace['input'] as $feat => $val): ?>
                        <tr>
                            <td><code><?= e($feat) ?></code></td>
                            <td><strong><?= is_float($val) ? rtrim(rtrim(number_format($val, 4, '.', ''), '0'), '.') : $val ?></strong></td>
                            <td>
                                <?php if (strpos($feat, 'gangguan_') === 0): ?>
                                    <small class="text-muted">One-hot encoding</small>
                                <?php else: ?>
                                    <small class="text-muted">Numerik asli</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================= -->
        <!-- 5. TAHAP 3 — VOTING TIAP POHON -->
        <!-- ============================= -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <strong>🌲 TAHAP 3 — Keputusan Setiap Pohon (<?= $trace['n_trees'] ?> pohon)</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Setiap pohon melakukan perjalanan dari <strong>root</strong> ke <strong>leaf</strong> 
                    berdasarkan aturan <code>if (nilai <= threshold)</code>. Klik pada tiap pohon untuk melihat detail jalurnya.
                </p>

                <!-- Ringkasan voting -->
                <div class="mb-3 text-center">
                    <?php foreach ($trace['votes'] as $label => $count): ?>
                        <span class="vote-box" style="background:<?= $label=='Ya'?'#fee2e2':'#d1fae5' ?>;color:<?= $label=='Ya'?'#991b1b':'#065f46' ?>">
                            <?= $label ?>: <?= $count ?> pohon
                        </span>
                    <?php endforeach; ?>
                </div>

                <!-- Akordeon tiap pohon -->
                <div class="accordion" id="accordionTrees">
                    <?php foreach ($trace['trees'] as $tree): ?>
                    <?php 
                        $voteClass = $tree['prediction'] == 'Ya' ? 'vote-ya' : 'vote-tidak';
                        $badgeCls  = $tree['prediction'] == 'Ya' ? 'danger' : 'success';
                    ?>
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#tree<?= $tree['nomor'] ?>">
                                <span class="me-3"><strong>Pohon #<?= $tree['nomor'] ?></strong></span>
                                <span class="badge bg-<?= $badgeCls ?> me-2">
                                    Vote: <?= $tree['prediction'] ?>
                                </span>
                                <small class="text-muted">
                                    (<?= count($tree['path']) ?> langkah keputusan)
                                </small>
                            </button>
                        </h2>
                        <div id="tree<?= $tree['nomor'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionTrees">
                            <div class="accordion-body">
                                <div class="tree-card <?= $voteClass ?>">
                                    <strong>Jalur Keputusan:</strong>
                                    <div class="mt-2">
                                        <?php if (empty($tree['path'])): ?>
                                            <div class="path-step">Pohon langsung menjadi leaf (tidak ada split)</div>
                                        <?php else: ?>
                                            <?php foreach ($tree['path'] as $i => $step): ?>
                                                <div class="path-step <?= strtolower($step['decision']) ?>">
                                                    <strong>Langkah <?= $i+1 ?>:</strong>
                                                    Apakah 
                                                    <code><?= e($step['feature']) ?></code> 
                                                    (<?= rtrim(rtrim(number_format($step['value'], 4, '.', ''), '0'), '.') ?>)
                                                    <strong><?= $step['operator'] ?></strong>
                                                    threshold 
                                                    <code><?= rtrim(rtrim(number_format($step['threshold'], 4, '.', ''), '0'), '.') ?></code>?
                                                    
                                                    → <strong class="text-<?= $step['decision']=='KIRI'?'primary':'warning' ?>">
                                                        <?= $step['decision'] ?>
                                                    </strong>

                                                    <?php if ($step['gini'] !== null): ?>
                                                        <small class="text-muted ms-2">
                                                            (Gini: <?= $step['gini'] ?>, Samples: <?= $step['samples'] ?>)
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <div class="path-step" style="background:#fff3cd;border-left-color:#f59e0b">
                                            <strong>🎯 Hasil:</strong> 
                                            Mencapai <strong>leaf</strong> → Vote 
                                            <span class="badge bg-<?= $badgeCls ?>"><?= $tree['prediction'] ?></span>
                                            <?php if (!empty($tree['leaf']['count'])): ?>
                                                <small class="text-muted">(<?= $tree['leaf']['count'] ?> sampel di leaf ini)</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ============================= -->
        <!-- 6. TAHAP 4 — VOTING AKHIR -->
        <!-- ============================= -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <strong>🗳️ TAHAP 4 — Majority Voting (Suara Terbanyak)</strong>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Random Forest menggabungkan hasil semua pohon dengan cara <strong>majority voting</strong>:
                    kelas dengan suara terbanyak menjadi hasil akhir.
                </p>

                <div class="formula-box">
                    Total Pohon = <strong><?= $trace['total_votes'] ?></strong><br>
                    <?php foreach ($trace['votes'] as $lbl => $cnt): ?>
                        Suara "<?= $lbl ?>" = <strong><?= $cnt ?></strong> pohon 
                        → <?= $trace['probability'][$lbl] ?>%<br>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($trace['votes'] as $lbl => $cnt): ?>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>
                                <span class="badge bg-<?= $lbl=='Ya'?'danger':'success' ?>"><?= $lbl ?></span>
                                — <?= $cnt ?> dari <?= $trace['total_votes'] ?> pohon
                            </span>
                            <strong><?= $trace['probability'][$lbl] ?>%</strong>
                        </div>
                        <div class="progress progress-tall">
                            <div class="progress-bar bg-<?= $lbl=='Ya'?'danger':'success' ?>" 
                                 style="width: <?= $trace['probability'][$lbl] ?>%">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="alert alert-<?= $trace['final']=='Ya'?'danger':'success' ?> mt-3 mb-0">
                    <strong>Kesimpulan Voting:</strong> 
                    Kelas mayoritas adalah <strong>"<?= $trace['final'] ?>"</strong> 
                    dengan probabilitas <strong><?= $trace['final_prob'] ?>%</strong>
                    (<code>max(votes) / total_trees * 100%</code>)
                </div>
            </div>
        </div>

        <!-- ============================= -->
        <!-- 7. RINGKASAN VOTE POHON -->
        <!-- ============================= -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <strong>📊 Ringkasan Vote Tiap Pohon</strong>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap">
                    <?php foreach ($trace['trees'] as $tree): ?>
                        <div class="text-center me-2 mb-2" style="min-width:60px">
                            <div class="small text-muted">#<?= $tree['nomor'] ?></div>
                            <div class="badge bg-<?= $tree['prediction']=='Ya'?'danger':'success' ?>" 
                                 style="width:100%; padding:8px">
                                <?= $tree['prediction'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ============================= -->
        <!-- 8. CATATAN METODOLOGI -->
        <!-- ============================= -->
        <div class="card mb-3">
            <div class="card-header bg-white"><strong>📚 Catatan Metodologi</strong></div>
            <div class="card-body small">
                <ol class="mb-0">
                    <li><strong>Bootstrap Sampling:</strong> Setiap pohon dilatih dengan sampel acak (dengan pengembalian) dari data training.</li>
                    <li><strong>Random Feature Selection:</strong> Saat split, setiap pohon hanya mempertimbangkan sebagian fitur acak (√n_features), mengurangi korelasi antar pohon.</li>
                    <li><strong>Gini Impurity:</strong> Setiap split dipilih berdasarkan nilai Gini terendah — semakin kecil Gini, semakin "murni" hasil pemisahan kelas.</li>
                    <li><strong>Majority Voting:</strong> Hasil akhir ditentukan oleh suara mayoritas dari semua pohon.</li>
                    <li><strong>Probabilitas:</strong> Dihitung dari proporsi pohon yang vote kelas tersebut. Contoh: 7 dari 10 pohon vote "Ya" → probabilitas Ya = 70%.</li>
                </ol>
            </div>
        </div>

        <?php else: ?>
        <div class="alert alert-warning">
            ⚠ Detail perhitungan tidak tersedia (session expired). Silakan lakukan prediksi ulang.
        </div>
        <?php endif; ?>

        <!-- ============================= -->
        <!-- ACTION BUTTONS -->
        <!-- ============================= -->
        <div class="text-center mb-4">
            <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak</button>
        </div>

    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>