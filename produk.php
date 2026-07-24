<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../includes/auth.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q !== '') {
    $like = "%{$q}%";
    $stmt = mysqli_prepare($conn, "
        SELECT p.id, p.kode_produk, p.nama_produk, p.gambar, k.nama_kategori
        FROM produk p
        JOIN kategori k ON k.id = p.kategori_id
        WHERE p.nama_produk LIKE ? OR p.kode_produk LIKE ?
        ORDER BY p.created_at DESC
    ");
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $produk = mysqli_stmt_get_result($stmt);
} else {
    $produk = mysqli_query($conn, "
        SELECT p.id, p.kode_produk, p.nama_produk, p.gambar, k.nama_kategori
        FROM produk p
        JOIN kategori k ON k.id = p.kategori_id
        ORDER BY p.created_at DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk — Katalog Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <p class="eyebrow">Kelola Katalog</p>
                    <h1>Produk</h1>
                    <p>Semua produk yang tampil di katalog.</p>
                </div>
                <a href="produk_tambah.php" class="btn">+ Tambah Produk</a>
            </div>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert-success">Produk berhasil dihapus.</div>
            <?php elseif (isset($_GET['updated'])): ?>
                <div class="alert-success">Produk berhasil diperbarui.</div>
            <?php elseif (isset($_GET['added'])): ?>
                <div class="alert-success">Produk berhasil ditambahkan.</div>
            <?php endif; ?>

            <div class="panel">
                <div class="panel-header">
                    <h3>Semua Produk</h3>
                    <form method="get" action="produk.php" style="display:flex; gap:8px;">
                        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama / kode produk..."
                               style="padding:8px 14px; border:1px solid var(--line); border-radius:999px; font-size:13px; min-width:220px;">
                        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
                    </form>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($produk) === 0): ?>
                            <tr class="empty-row"><td colspan="4">Tidak ada produk ditemukan.</td></tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($produk)): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <?php if ($row['gambar']): ?>
                                                <img class="thumb" src="../assets/uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="">
                                            <?php else: ?>
                                                <div class="thumb-placeholder">N/A</div>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($row['nama_produk']) ?>
                                        </div>
                                    </td>
                                    <td class="muted"><?= htmlspecialchars($row['kode_produk']) ?></td>
                                    <td><span class="badge"><?= htmlspecialchars($row['nama_kategori']) ?></span></td>
                                    <td class="actions-cell">
                                        <a href="produk_edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <a href="produk_hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                           onclick="return confirm('Hapus produk ini?');">Hapus</a>
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
