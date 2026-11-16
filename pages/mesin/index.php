<?php
$page_title = 'Data Mesin';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$rows  = $pdo->query("SELECT * FROM mesin ORDER BY kode_mesin")->fetchAll();
$flash = get_flash();
?>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>📋 Daftar Mesin Canon C10000</strong>
        <a href="tambah.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Tambah Mesin</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered datatable">
            <thead class="table-light">
                <tr>
                    <th>Kode</th><th>Nama Mesin</th><th>Merk</th><th>Tipe</th>
                    <th>Tahun</th><th>Status</th><th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><strong><?= e($r['kode_mesin']) ?></strong></td>
                    <td><?= e($r['nama_mesin']) ?></td>
                    <td><?= e($r['merk']) ?></td>
                    <td><?= e($r['tipe']) ?></td>
                    <td><?= e($r['tahun_instalasi']) ?></td>
                    <td>
                        <span class="badge bg-<?= $r['status']=='aktif'?'success':($r['status']=='maintenance'?'warning':'danger') ?>">
                            <?= e($r['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $r['id_mesin'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <a href="hapus.php?id=<?= $r['id_mesin'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus mesin ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>