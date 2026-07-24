<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../includes/auth.php';

$error = "";

// --- hapus user ---
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    if ($id === (int) $_SESSION['user_id']) {
        $error = "Tidak bisa menghapus akun yang sedang digunakan";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        header("Location: user.php?deleted=1");
        exit;
    }
}

// --- tambah user ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $username, $hash);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: user.php?added=1");
            exit;
        } else {
            $error = "Username sudah digunakan";
        }
    }
}

$users = mysqli_query($conn, "SELECT id, username, created_at FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Admin — Katalog Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <p class="eyebrow">Pengaturan</p>
                    <h1>User Admin</h1>
                    <p>Kelola akun yang dapat mengakses panel admin ini.</p>
                </div>
            </div>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert-success">User berhasil dihapus.</div>
            <?php elseif (isset($_GET['added'])): ?>
                <div class="alert-success">User berhasil ditambahkan.</div>
            <?php endif; ?>

            <div class="panel">
                <div class="panel-header">
                    <h3>Tambah User Baru</h3>
                </div>
                <div style="padding:24px;">
                    <?php if ($error): ?>
                        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="user.php">
                        <div class="form-row">
                            <div class="field-group">
                                <label>Username</label>
                                <input type="text" name="username" placeholder="Username admin" required>
                            </div>
                            <div class="field-group">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>
                        <div class="form-actions" style="margin-top:0;">
                            <button type="submit" class="btn btn-sm">Tambah User</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h3>Semua User</h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($row['username']) ?>
                                    <?php if ($row['id'] == $_SESSION['user_id']): ?>
                                        <span class="badge" style="margin-left:8px;"><span class="dot" style="background:var(--gold)"></span>Anda</span>
                                    <?php endif; ?>
                                </td>
                                <td class="muted"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td class="actions-cell">
                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                        <a href="user.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                           onclick="return confirm('Hapus user ini?');">Hapus</a>
                                    <?php else: ?>
                                        <span class="muted" style="font-size:13px;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
