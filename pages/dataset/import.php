<?php
$page_title = 'Import Dataset CSV';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    
    if (!is_uploaded_file($file)) {
        set_flash('danger', 'Gagal upload file.');
        redirect('pages/dataset/import.php');
    }

    $handle = fopen($file, 'r');
    if (!$handle) {
        set_flash('danger', 'File tidak bisa dibaca.');
        redirect('pages/dataset/import.php');
    }

    $idMesin  = (int)$_POST['id_mesin'];
    $header   = fgetcsv($handle); // skip header
    $count    = 0;
    $errors   = 0;

    $stmt = $pdo->prepare("INSERT INTO dataset 
        (id_mesin, tanggal, waktu_downtime, jam_operasi, suhu_mesin, getaran, 
         konsumsi_listrik, jumlah_cetak, umur_komponen, downtime, 
         jenis_gangguan, frekuensi_kerusakan, status_downtime)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 11) { $errors++; continue; }
        try {
            $stmt->execute([
                $idMesin,
                $row[0],
                !empty($row[1]) ? $row[1] : null,
                (float)$row[2],
                (float)$row[3],
                (float)$row[4],
                (float)$row[5],
                0,
                (int)$row[6],
                (int)$row[7],
                $row[8],
                (int)$row[9],
                $row[10]
            ]);
            $count++;
        } catch (Exception $e) {
            $errors++;
        }
    }
    fclose($handle);

    set_flash('success', "Import selesai: $count baris berhasil, $errors gagal.");
    redirect('pages/dataset/index.php');
}

$mesinList = $pdo->query("SELECT * FROM mesin")->fetchAll();
?>
<div class="card">
    <div class="card-header bg-white"><strong>Import Dataset dari CSV</strong></div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>📋 Format CSV (11 kolom, tanpa header di baris pertama):</strong>
            <pre class="mb-2 mt-2" style="background:#f8f9fa;padding:10px;border-radius:5px;font-size:12px">tanggal,waktu_downtime,jam_operasi,suhu_mesin,getaran,konsumsi_listrik,umur_komponen,downtime,jenis_gangguan,frekuensi_kerusakan,status_downtime
2024-01-01,,8.0,45.2,2.1,12.5,30,0,Tidak Ada,0,Tidak
2024-01-02,10:25:00,9.5,52.8,3.8,14.2,45,25,Overheat,1,Ya</pre>
            <small><strong>Catatan:</strong> 
                <br>• Kolom <code>waktu_downtime</code> boleh kosong
                <br>• <code>jenis_gangguan</code>: Paper Jam / Overheat / Getaran Berlebih / Kerusakan Mekanik / Gangguan Listrik / Maintenance Rutin / Tidak Ada
                <br>• <code>status_downtime</code>: Ya / Tidak
            </small>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pilih Mesin</label>
                    <select name="id_mesin" class="form-select" required>
                        <?php foreach ($mesinList as $m): ?>
                            <option value="<?= $m['id_mesin'] ?>"><?= e($m['nama_mesin']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">File CSV</label>
                    <input type="file" name="file" class="form-control" accept=".csv" required>
                </div>
            </div>
            <button class="btn btn-success"><i class="bi bi-upload"></i> Import Sekarang</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>