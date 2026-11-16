<?php
$page_title = 'Dataset Historis Downtime';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$rows = $pdo->query("
    SELECT d.*, m.nama_mesin 
    FROM dataset d 
    JOIN mesin m ON d.id_mesin = m.id_mesin 
    ORDER BY d.tanggal DESC
")->fetchAll();

$mesinList = $pdo->query("SELECT * FROM mesin")->fetchAll();
$flash = get_flash();

$jenisList = ['Paper Jam','Overheat','Getaran Berlebih','Kerusakan Mekanik','Gangguan Listrik','Maintenance Rutin','Tidak Ada'];
?>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div><?php endif; ?>

<div class="card mb-3">
    <div class="card-header bg-white">
        <strong>➕ Tambah Data Historis</strong>
        <span class="text-muted small float-end">Sesuai 5 variabel penelitian</span>
    </div>
    <div class="card-body">
        <form method="POST" action="simpan.php">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="small">Mesin</label>
                    <select name="id_mesin" class="form-select form-select-sm" required>
                        <option value="">- Pilih -</option>
                        <?php foreach ($mesinList as $m): ?>
                            <option value="<?= $m['id_mesin'] ?>"><?= e($m['nama_mesin']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-2">
                    <label class="small">Waktu Downtime</label>
                    <input type="time" name="waktu_downtime" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <label class="small">Jam Operasi</label>
                    <input type="number" step="0.1" name="jam_operasi" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-1">
                    <label class="small">Suhu (°C)</label>
                    <input type="number" step="0.1" name="suhu_mesin" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-1">
                    <label class="small">Getaran</label>
                    <input type="number" step="0.1" name="getaran" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-1">
                    <label class="small">kWh</label>
                    <input type="number" step="0.1" name="konsumsi_listrik" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-1">
                    <label class="small">Umur (hari)</label>
                    <input type="number" name="umur_komponen" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-1">
                    <label class="small">Durasi (mnt)</label>
                    <input type="number" name="downtime" class="form-control form-control-sm" value="0">
                </div>
                <div class="col-md-2">
                    <label class="small">Jenis Gangguan</label>
                    <select name="jenis_gangguan" class="form-select form-select-sm">
                        <?php foreach ($jenisList as $j): ?>
                            <option value="<?= $j ?>"><?= $j ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">Frekuensi Kerusakan</label>
                    <input type="number" name="frekuensi_kerusakan" class="form-control form-control-sm" value="0">
                </div>
                <div class="col-md-2">
                    <label class="small">Status Downtime</label>
                    <select name="status_downtime" class="form-select form-select-sm" required>
                        <option value="Tidak">Tidak</option>
                        <option value="Ya">Ya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">&nbsp;</label>
                    <button class="btn btn-primary btn-sm w-100"><i class="bi bi-plus-lg"></i> Tambah Data</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>📊 Data Historis (<?= count($rows) ?> baris)</strong>
        <a href="import.php" class="btn btn-sm btn-success"><i class="bi bi-upload"></i> Import CSV</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered table-sm datatable">
            <thead class="table-light">
    <tr>
        <th>Tgl</th>          <!-- 1 -->
        <th>Mesin</th>        <!-- 2 -->
        <th>Waktu</th>        <!-- 3 -->
        <th>Jam</th>          <!-- 4 -->
        <th>Suhu</th>         <!-- 5 -->
        <th>Getar</th>        <!-- 6 -->
        <th>kWh</th>          <!-- 7 -->
        <th>Umur</th>         <!-- 8 -->
        <th>Durasi</th>       <!-- 9 -->
        <th>Gangguan</th>     <!-- 10 -->
        <th>Frek</th>         <!-- 11 -->
        <th>Status</th>       <!-- 12 -->
        <th>Aksi</th>         <!-- 13 -->
    </tr>
</thead>
<tbody>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= date('d/m/y', strtotime($r['tanggal'])) ?></td>       <!-- 1 -->
        <td><small><?= e($r['nama_mesin']) ?></small></td>              <!-- 2 -->
        <td><?= $r['waktu_downtime'] ? substr($r['waktu_downtime'],0,5) : '-' ?></td> <!-- 3 -->
        <td><?= $r['jam_operasi'] ?></td>                               <!-- 4 -->
        <td><?= $r['suhu_mesin'] ?></td>                                <!-- 5 -->
        <td><?= $r['getaran'] ?></td>                                   <!-- 6 -->
        <td><?= $r['konsumsi_listrik'] ?></td>                          <!-- 7 -->
        <td><?= $r['umur_komponen'] ?></td>                             <!-- 8 -->
        <td><?= $r['downtime'] ?></td>                                  <!-- 9 -->
        <td><small><span class="badge bg-info"><?= e($r['jenis_gangguan']) ?></span></small></td> <!-- 10 -->
        <td><?= $r['frekuensi_kerusakan'] ?>x</td>                      <!-- 11 -->
        <td>
            <span class="badge bg-<?= $r['status_downtime']=='Ya'?'danger':'success' ?>">
                <?= $r['status_downtime'] ?>
            </span>
        </td>                                                            <!-- 12 -->
        <td>
            <a href="edit.php?id=<?= $r['id_data'] ?>" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i>
            </a>
            <a href="hapus.php?id=<?= $r['id_data'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">
                <i class="bi bi-trash"></i>
            </a>
        </td>                                                            <!-- 13 -->
    </tr>
    <?php endforeach; ?>
</tbody>
        </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>