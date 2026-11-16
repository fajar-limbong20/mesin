<?php
$page_title = 'Manajemen User';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_admin();

$rows  = $pdo->query("SELECT * FROM users ORDER BY id_user")->fetchAll();
$flash = get_flash();
?>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div><?php endif; ?>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>👥 Daftar User</strong>
        <a href="tambah.php" class="btn btn-sm btn-primary"><i class="bi bi-person-plus"></i> Tambah User</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered datatable">
            <thead class="table-light">
                <tr>
                    <th width="60">ID</th><th>Nama</th><th>Username</th>
                    <th>Level</th><th>Dibuat</th><th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id_user'] ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['username']) ?></td>
                    <td>
                        <span class="badge bg-<?= $r['level']=='admin'?'danger':'primary' ?>">
                            <?= e($r['level']) ?>
                        </span>
                    </td>
                    <td><small><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small></td>
                    <td>
                        <a href="edit.php?id=<?= $r['id_user'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <?php if ($r['id_user'] != $_SESSION['user_id']): ?>
                            <a href="hapus.php?id=<?= $r['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>