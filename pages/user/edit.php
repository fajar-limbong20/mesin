
<?php
$page_title = 'Edit User';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$id]);
$u = $stmt->fetch();

if (!$u) {
    set_flash('danger', 'User tidak ditemukan');
    redirect('pages/user/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $level    = $_POST['level'];
    $password = $_POST['password'];

    // Cek username sudah digunakan user lain
    $cek = $pdo->prepare(
        "SELECT id_user FROM users WHERE username = ? AND id_user != ?"
    );
    $cek->execute([$username, $id]);

    if ($cek->fetch()) {
        set_flash('danger', 'Username sudah digunakan.');
    } else {
        if (!empty($password)) {
            $stmt = $pdo->prepare("
                UPDATE users
                SET nama=?, username=?, level=?, password=MD5(?)
                WHERE id_user=?
            ");
            $stmt->execute([
                $nama, $username, $level, $password, $id
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE users
                SET nama=?, username=?, level=?
                WHERE id_user=?
            ");
            $stmt->execute([
                $nama, $username, $level, $id
            ]);
        }

        set_flash('success', 'User berhasil diupdate.');
        redirect('pages/user/index.php');
    }
}
?>

<div class="card">
    <div class="card-header bg-white">
        <strong>Edit User</strong>
    </div>

    <div class="card-body">
        <form method="POST">
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        value="<?= e($u['nama']) ?>"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        value="<?= e($u['username']) ?>"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Password Baru
                        <small class="text-muted">
                            (kosongkan jika tidak diubah)
                        </small>
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        minlength="5"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select" required>
                        <option value="teknisi"
                            <?= $u['level']=='teknisi'?'selected':'' ?>>
                            Teknisi
                        </option>
                        <option value="admin"
                            <?= $u['level']=='admin'?'selected':'' ?>>
                            Admin
                        </option>
                    </select>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update
            </button>

            <a href="index.php" class="btn btn-secondary">
                Batal
            </a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
