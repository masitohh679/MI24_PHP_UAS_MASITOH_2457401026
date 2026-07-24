<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

$current = basename($_SERVER['PHP_SELF']);

function nav_active($page, $current) {
    return $page === $current ? 'active' : '';
}
?>
<aside class="admin-sidebar">
    <div class="brand">
        <span>Katalog Online</span>
        Admin Panel
    </div>

    <nav class="admin-nav">
        <a href="index.php" class="<?= nav_active('index.php', $current) ?>">
            <span class="dot" style="background:var(--gold)"></span>Dashboard
        </a>
        <a href="kategori.php" class="<?= nav_active('kategori.php', $current) ?>">
            <span class="dot" style="background:var(--swatch-sepatu)"></span>Kategori
        </a>
        <a href="produk.php" class="<?= nav_active('produk.php', $current) ?>">
            <span class="dot" style="background:var(--swatch-kosmetik)"></span>Produk
        </a>
        <a href="user.php" class="<?= nav_active('user.php', $current) ?>">
            <span class="dot" style="background:var(--swatch-tas)"></span>User Admin
        </a>
    </nav>

    <div class="who">
        <div class="name"><?= htmlspecialchars($_SESSION['username']) ?></div>
        <a href="../logout.php">Logout &rarr;</a>
    </div>
</aside>
