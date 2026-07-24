<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT gambar FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($produk) {
    if ($produk['gambar']) {
        $file = __DIR__ . '/../assets/uploads/' . $produk['gambar'];
        if (file_exists($file)) unlink($file);
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM produk WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: produk.php?deleted=1");
exit;
?>
