<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../includes/auth.php';

$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM kategori"))['n'];
$total_produk   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM produk"))['n'];
$total_user     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM users"))['n'];

$recent = mysqli_query($conn, "
    SELECT p.kode_produk, p.nama_produk, p.gambar, p.created_at, k.nama_kategori
    FROM produk p
    JOIN kategori k ON k.id = p.kategori_id
    ORDER BY p.created_at DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Katalog Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <p class="eyebrow">Dashboard</p>
                    <h1>Halo, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
                    <p>Ringkasan katalog produk Anda saat ini.</p>
                </div>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <p class="label">Total Kategori</p>
                    <div class="value"><?= $total_kategori ?></div>
                </div>
                <div class="stat-card">
                    <p class="label">Total Produk</p>
                    <div class="value"><?= $total_produk ?></div>
                </div>
                <div class="stat-card">
                    <p class="label">Total User Admin</p>
                    <div class="value"><?= $total_user ?></div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h3>Produk Terbaru</h3>
                    <a href="produk.php" class="btn btn-secondary btn-sm">Lihat Semua</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Ditambahkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($recent) === 0): ?>
                            <tr class="empty-row"><td colspan="4">Belum ada produk.</td></tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($recent)): ?>
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
                                    <td class="muted"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
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
