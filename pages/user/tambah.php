<?php
$page_title = 'Tambah User';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $level    = $_POST['level'];

    // Cek username unik
    $cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $cek->execute([$username]);
    if ($cek->fetchColumn() > 0) {
        set_flash('danger', 'Username sudah dipakai!');
        redirect('pages/user/tambah.php');
    }

    $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, level) VALUES (?,?,MD5(?),?)");
    $stmt->execute([$nama, $username, $password, $level]);
    set_flash('success', 'User berhasil ditambahkan.');
    redirect('pages/user/index.php');
}
?>
<div class="card">
    <div class="card-header bg-white"><strong>Tambah User Baru</strong></div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input name="nama" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input name="username" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="5">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select" required>
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>