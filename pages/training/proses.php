<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../library/RandomForest.php';

// ---- 1. Validasi input
$nama       = trim($_POST['nama_model'] ?? 'Model RF');
$nTrees     = max(1, (int)($_POST['n_trees'] ?? 10));
$maxDepth   = max(1, (int)($_POST['max_depth'] ?? 5));
$minSamples = max(1, (int)($_POST['min_samples'] ?? 2));
$rasio      = (float)($_POST['rasio'] ?? 0.8);

// ---- 2. Ambil dataset
$rows = $pdo->query("SELECT * FROM dataset")->fetchAll();
if (count($rows) < 5) {
    set_flash('danger', 'Data historis minimal 5 baris untuk training.');
    redirect('pages/training/index.php');
}

// ---- 3. Encode jenis_gangguan (One-Hot Encoding)
$jenisList = ['Paper Jam','Overheat','Getaran Berlebih','Kerusakan Mekanik','Gangguan Listrik','Maintenance Rutin'];

// Daftar fitur
$features = [
    'jam_operasi','suhu_mesin','getaran','konsumsi_listrik',
    'umur_komponen','downtime','frekuensi_kerusakan'
];
foreach ($jenisList as $j) {
    $features[] = 'gangguan_' . preg_replace('/[^a-z]/i','_', strtolower($j));
}

// Bangun matriks X & vektor y
$X = []; $y = [];
foreach ($rows as $r) {
    $fitur = [
        'jam_operasi'         => (float)$r['jam_operasi'],
        'suhu_mesin'          => (float)$r['suhu_mesin'],
        'getaran'             => (float)$r['getaran'],
        'konsumsi_listrik'    => (float)$r['konsumsi_listrik'],
        'umur_komponen'       => (float)$r['umur_komponen'],
        'downtime'            => (float)$r['downtime'],
        'frekuensi_kerusakan' => (float)$r['frekuensi_kerusakan'],
    ];
    foreach ($jenisList as $j) {
        $key = 'gangguan_' . preg_replace('/[^a-z]/i','_', strtolower($j));
        $fitur[$key] = ($r['jenis_gangguan'] === $j) ? 1 : 0;
    }
    $X[] = $fitur;
    $y[] = $r['status_downtime'];
}

// ---- 4. Split training/testing (shuffle acak)
$idx = range(0, count($X) - 1);
shuffle($idx);
$nTrain = (int)floor(count($X) * $rasio);
$trainIdx = array_slice($idx, 0, $nTrain);
$testIdx  = array_slice($idx, $nTrain);

// Simpan hasil split ke tabel preprocessing
$pdo->exec("DELETE FROM preprocessing");
$stmtPrep = $pdo->prepare("INSERT INTO preprocessing (id_data, tipe_data) VALUES (?,?)");
foreach ($trainIdx as $i) $stmtPrep->execute([$rows[$i]['id_data'], 'training']);
foreach ($testIdx  as $i) $stmtPrep->execute([$rows[$i]['id_data'], 'testing']);

$XTrain = $yTrain = $XTest = $yTest = [];
foreach ($trainIdx as $i) { $XTrain[] = $X[$i]; $yTrain[] = $y[$i]; }
foreach ($testIdx  as $i) { $XTest[]  = $X[$i]; $yTest[]  = $y[$i]; }

// ---- 5. Training Random Forest
$rf = new RandomForest($nTrees, $maxDepth, $minSamples);
$rf->fit($XTrain, $yTrain, $features);

// ---- 6. Evaluasi pada data testing
$tp = $tn = $fp = $fn = 0;
foreach ($XTest as $i => $row) {
    $pred = $rf->predict($row);
    $act  = $yTest[$i];
    if     ($act === 'Ya'    && $pred === 'Ya')    $tp++;
    elseif ($act === 'Tidak' && $pred === 'Tidak') $tn++;
    elseif ($act === 'Tidak' && $pred === 'Ya')    $fp++;
    elseif ($act === 'Ya'    && $pred === 'Tidak') $fn++;
}

$total     = $tp + $tn + $fp + $fn;
$akurasi   = $total > 0 ? round(($tp + $tn) / $total * 100, 2) : 0;
$precision = ($tp + $fp) > 0 ? round($tp / ($tp + $fp) * 100, 2) : 0;
$recall    = ($tp + $fn) > 0 ? round($tp / ($tp + $fn) * 100, 2) : 0;
$f1        = ($precision + $recall) > 0 ? round(2 * $precision * $recall / ($precision + $recall), 2) : 0;

// ---- 7. Simpan model
$pdo->exec("UPDATE model_rf SET status='nonaktif'");

$stmt = $pdo->prepare("INSERT INTO model_rf 
    (nama_model, n_trees, max_depth, min_samples, rasio_split, 
     akurasi, precision_score, recall_score, f1_score, model_json, status)
    VALUES (?,?,?,?,?,?,?,?,?,?, 'aktif')");
$stmt->execute([
    $nama, $nTrees, $maxDepth, $minSamples, $rasio,
    $akurasi, $precision, $recall, $f1, $rf->toJson()
]);
$idModel = $pdo->lastInsertId();

// ---- 8. Simpan evaluasi
$pdo->prepare("INSERT INTO evaluasi (id_model, tp, tn, fp, fn, akurasi) VALUES (?,?,?,?,?,?)")
    ->execute([$idModel, $tp, $tn, $fp, $fn, $akurasi]);

// ---- 9. Simpan detail pohon
$detailStmt = $pdo->prepare("INSERT INTO detail_tree (id_model, nomor_tree, struktur_json) VALUES (?,?,?)");
foreach ($rf->trees as $i => $t) {
    $detailStmt->execute([$idModel, $i + 1, json_encode($t->toArray())]);
}

// ---- 10. Log aktivitas
$pdo->prepare("INSERT INTO log_aktivitas (id_user, aktivitas) VALUES (?,?)")
    ->execute([$_SESSION['user_id'], "Training RF: $nama (akurasi $akurasi%)"]);

set_flash('success', "✅ Training selesai! Akurasi: {$akurasi}% | F1-Score: {$f1}%");
redirect('pages/training/detail.php?id=' . $idModel);