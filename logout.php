<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once "config.php";
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>
