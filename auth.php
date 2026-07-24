<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}
?>
