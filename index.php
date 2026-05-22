<?php
session_start();

/* ========================================================
   LOGIC: ALUR MASUK PERTAMA KALI (GUEST ACCESS)
======================================================== */

// Jika user belum login dan belum terdaftar sebagai guest, set session role menjadi guest
if (!isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    $_SESSION['role'] = 'guest';
    $_SESSION['nama_user'] = 'Guest';
}

// Langsung arahkan semuanya ke beranda.php / home.php
header("Location: beranda.php");
exit;
?>