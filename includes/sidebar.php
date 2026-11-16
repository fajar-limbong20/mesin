<?php
$current = $_SERVER['PHP_SELF'];
function isActive($needle, $current) { return strpos($current, $needle) !== false ? 'active' : ''; }
$user = current_user();
?>
<div class="col-md-2 sidebar p-0">
    <div class="brand"><i class="bi bi-cpu"></i> RF Canon C10000</div>
    <nav class="p-3">
        <div class="text-uppercase small text-muted mb-2 px-2">Menu Utama</div>
        <a href="<?= BASE_URL ?>pages/dashboard.php" class="<?= isActive('/dashboard', $current) ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="<?= BASE_URL ?>pages/mesin/index.php" class="<?= isActive('/mesin/', $current) ?>">
            <i class="bi bi-printer"></i> Data Mesin
        </a>
        <a href="<?= BASE_URL ?>pages/dataset/index.php" class="<?= isActive('/dataset/', $current) ?>">
            <i class="bi bi-database"></i> Dataset Historis
        </a>

        <div class="text-uppercase small text-muted mt-3 mb-2 px-2">Random Forest</div>
        <a href="<?= BASE_URL ?>pages/training/index.php" class="<?= isActive('/training/', $current) ?>">
            <i class="bi bi-diagram-3"></i> Training Model
        </a>
        <a href="<?= BASE_URL ?>pages/prediksi/index.php" class="<?= isActive('/prediksi/', $current) ?>">
            <i class="bi bi-magic"></i> Prediksi Downtime
        </a>

        <div class="text-uppercase small text-muted mt-3 mb-2 px-2">Laporan</div>
        <a href="<?= BASE_URL ?>pages/laporan/index.php" class="<?= isActive('/laporan/', $current) ?>">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan
        </a>

        <?php if ($user['level'] === 'admin'): ?>
        <div class="text-uppercase small text-muted mt-3 mb-2 px-2">Administrator</div>
        <a href="<?= BASE_URL ?>pages/user/index.php" class="<?= isActive('/user/', $current) ?>">
            <i class="bi bi-people"></i> Manajemen User
        </a>
        <a href="<?= BASE_URL ?>pages/log/index.php" class="<?= isActive('/log/', $current) ?>">
            <i class="bi bi-clock-history"></i> Log Aktivitas
        </a>
        <?php endif; ?>

        <hr style="border-color:#334155">
        <a href="<?= BASE_URL ?>auth/logout.php" onclick="return confirm('Yakin logout?')">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
</div>

<div class="col-md-10 p-0">
    <nav class="navbar navbar-light bg-white shadow-sm px-4 mb-0">
        <span class="navbar-brand mb-0 h5"><?= e($page_title) ?></span>
        <span class="text-muted small">
            <i class="bi bi-person-circle"></i> <?= e($user['nama']) ?> 
            <span class="badge bg-secondary"><?= e($user['level']) ?></span>
        </span>
    </nav>
    <div class="content">