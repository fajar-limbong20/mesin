<?php
require_once __DIR__ . '/../../includes/auth.php';

$stmt = $pdo->prepare("INSERT INTO dataset 
    (id_mesin, tanggal, waktu_downtime, jam_operasi, suhu_mesin, getaran, 
     konsumsi_listrik, jumlah_cetak, umur_komponen, downtime, 
     jenis_gangguan, frekuensi_kerusakan, status_downtime) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->execute([
    (int)$_POST['id_mesin'],
    $_POST['tanggal'],
    !empty($_POST['waktu_downtime']) ? $_POST['waktu_downtime'] : null,
    (float)$_POST['jam_operasi'],
    (float)$_POST['suhu_mesin'],
    (float)$_POST['getaran'],
    (float)$_POST['konsumsi_listrik'],
    0,
    (int)$_POST['umur_komponen'],
    (int)$_POST['downtime'],
    $_POST['jenis_gangguan'],
    (int)$_POST['frekuensi_kerusakan'],
    $_POST['status_downtime']
]);

set_flash('success', 'Data historis berhasil ditambahkan.');
redirect('pages/dataset/index.php');