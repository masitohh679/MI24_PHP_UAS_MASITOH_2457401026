<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../includes/auth.php';

$error   = "";
$success = "";

// --- hapus kategori ---
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM kategori WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: kategori.php?deleted=1");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id            = (int) $_POST['edit_id'];
    $nama_kategori = trim($_POST['nama_kategori']);

    if ($nama_kategori === '') {
        $error = "Nama kategori tidak boleh kosong";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE kategori SET nama_kategori = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nama_kategori, $id);
        mysqli_stmt_execute($stmt);
        header("Location: kategori.php?updated=1");
        exit;
    }
}

$edit_row = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT id, nama_kategori FROM kategori WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$kategori = mysqli_query($conn, "
    SELECT k.id, k.nama_kategori, k.created_at, COUNT(p.id) AS jumlah_produk
    FROM kategori k
    LEFT JOIN produk p ON p.kategori_id = k.id
    GROUP BY k.id
    ORDER BY k.nama_kategori ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori — Katalog Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <p class="eyebrow">Kelola Katalog</p>
                    <h1>Kategori</h1>
                    <p>Atur kategori yang digunakan untuk mengelompokkan produk.</p>
                </div>
                <a href="kategori_tambah.php" class="btn">+ Tambah Kategori</a>
            </div>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert-success">Kategori berhasil dihapus.</div>
            <?php elseif (isset($_GET['updated'])): ?>
                <div class="alert-success">Kategori berhasil diperbarui.</div>
            <?php elseif (isset($_GET['added'])): ?>
                <div class="alert-success">Kategori berhasil ditambahkan.</div>
            <?php endif; ?>

            <?php if ($edit_row): ?>
                <div class="form-panel" style="margin-bottom:28px;">
                    <?php if ($error): ?>
                        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="kategori.php">
                        <input type="hidden" name="edit_id" value="<?= $edit_row['id'] ?>">
                        <div class="field-group">
                            <label>Edit Nama Kategori</label>
                            <input type="text" name="nama_kategori" value="<?= htmlspecialchars($edit_row['nama_kategori']) ?>" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-sm">Simpan Perubahan</button>
                            <a href="kategori.php" class="btn btn-secondary btn-sm">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="panel">
                <div class="panel-header">
                    <h3>Semua Kategori</h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Jumlah Produk</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($kategori) === 0): ?>
                            <tr class="empty-row"><td colspan="4">Belum ada kategori.</td></tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                    <td class="muted"><?= $row['jumlah_produk'] ?> produk</td>
                                    <td class="muted"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <td class="actions-cell">
                                        <a href="kategori.php?edit=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <a href="kategori.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                           onclick="return confirm('Hapus kategori ini? Produk di dalamnya akan ikut terhapus.');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
