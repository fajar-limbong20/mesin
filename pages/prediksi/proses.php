<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../library/RandomForest.php';

// ---- Ambil model aktif
$model = $pdo->query("SELECT * FROM model_rf WHERE status='aktif' ORDER BY akurasi DESC LIMIT 1")->fetch();
if (!$model) {
    set_flash('danger', 'Belum ada model aktif. Silakan training terlebih dahulu.');
    redirect('pages/prediksi/index.php');
}

// ---- Load model
$rf = new RandomForest();
if (!$rf->loadJson($model['model_json'])) {
    set_flash('danger', 'Model corrupt, silakan training ulang.');
    redirect('pages/prediksi/index.php');
}

// ---- Build input fitur (sama persis dengan training)
$jenisList = ['Paper Jam','Overheat','Getaran Berlebih','Kerusakan Mekanik','Gangguan Listrik','Maintenance Rutin'];

$input = [
    'jam_operasi'         => (float)$_POST['jam_operasi'],
    'suhu_mesin'          => (float)$_POST['suhu_mesin'],
    'getaran'             => (float)$_POST['getaran'],
    'konsumsi_listrik'    => (float)$_POST['konsumsi_listrik'],
    'umur_komponen'       => (float)$_POST['umur_komponen'],
    'downtime'            => (float)$_POST['downtime'],
    'frekuensi_kerusakan' => (float)$_POST['frekuensi_kerusakan'],
];

foreach ($jenisList as $j) {
    $key = 'gangguan_' . preg_replace('/[^a-z]/i','_', strtolower($j));
    $input[$key] = ($_POST['jenis_gangguan'] === $j) ? 1 : 0;
}

// ---- Prediksi + trace lengkap
$trace = $rf->predictWithTrace($input);
$label = $trace['final'];
$prob  = $trace['final_prob'];

// ---- Rekomendasi
if ($label === 'Ya') {
    $rekomendasi = "⚠ POTENSI DOWNTIME TERDETEKSI (Probabilitas: {$prob}%).\n\n"
                 . "Rekomendasi tindakan:\n"
                 . "1. Segera lakukan pengecekan preventif pada mesin Canon C10000\n"
                 . "2. Periksa suhu mesin (saat ini {$_POST['suhu_mesin']}°C)\n"
                 . "3. Periksa tingkat getaran (saat ini {$_POST['getaran']} mm/s)\n"
                 . "4. Cek umur komponen (saat ini {$_POST['umur_komponen']} hari)\n"
                 . "5. Siapkan tim maintenance untuk antisipasi";
} else {
    $rekomendasi = "✓ KONDISI MESIN NORMAL (Probabilitas: {$prob}%).\n\n"
                 . "Mesin diprediksi tidak akan mengalami downtime.\n"
                 . "Lanjutkan operasi rutin dan monitoring berkala.";
}

// ---- Simpan ke tabel prediksi
$pdo->prepare("INSERT INTO prediksi 
    (id_model, id_mesin, jam_operasi, suhu_mesin, getaran, konsumsi_listrik, 
     umur_komponen, downtime, frekuensi_kerusakan, jenis_gangguan, 
     hasil_prediksi, probabilitas, rekomendasi)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
    ->execute([
        $model['id_model'],
        (int)$_POST['id_mesin'],
        $input['jam_operasi'],
        $input['suhu_mesin'],
        $input['getaran'],
        $input['konsumsi_listrik'],
        (int)$input['umur_komponen'],
        (int)$input['downtime'],
        (int)$input['frekuensi_kerusakan'],
        $_POST['jenis_gangguan'],
        $label,
        $prob,
        $rekomendasi
    ]);

$idPred = $pdo->lastInsertId();

// ---- Simpan trace ke session untuk ditampilkan di hasil.php
$_SESSION['pred_trace_' . $idPred] = [
    'trace'    => $trace,
    'input'    => $input,
    'raw_post' => $_POST,
    'model'    => [
        'nama_model' => $model['nama_model'],
        'n_trees'    => $model['n_trees'],
        'max_depth'  => $model['max_depth'],
        'akurasi'    => $model['akurasi'],
    ],
];

// ---- Log
$pdo->prepare("INSERT INTO log_aktivitas (id_user, aktivitas) VALUES (?,?)")
    ->execute([$_SESSION['user_id'], "Prediksi mesin #{$_POST['id_mesin']}: $label ({$prob}%)"]);

redirect('pages/prediksi/hasil.php?id=' . $idPred);