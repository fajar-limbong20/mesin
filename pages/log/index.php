<?php
$page_title = 'Log Aktivitas';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_admin();

$rows = $pdo->query("
    SELECT l.*, u.nama, u.level 
    FROM log_aktivitas l
    LEFT JOIN users u ON l.id_user = u.id_user
    ORDER BY l.waktu DESC
    LIMIT 500
")->fetchAll();
?>
<div class="card">
    <div class="card-header bg-white"><strong>📝 Log Aktivitas (<?= count($rows) ?> entri terakhir)</strong></div>
    <div class="card-body">
        <table class="table table-bordered table-sm datatable">
            <thead class="table-light">
                <tr>
                    <th width="60">ID</th>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Level</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r['id_log'] ?></td>
                    <td><small><?= date('d/m/Y H:i:s', strtotime($r['waktu'])) ?></small></td>
                    <td><?= e($r['nama'] ?? 'System') ?></td>
                    <td>
                        <?php if ($r['level']): ?>
                            <span class="badge bg-<?= $r['level']=='admin'?'danger':'primary' ?>">
                                <?= e($r['level']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($r['aktivitas']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>