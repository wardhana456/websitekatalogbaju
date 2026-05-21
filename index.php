<?php
session_start();

/* =========================
CEK STATUS LOGIN
========================= */

// Jika sudah login → masuk ke beranda
if (isset($_SESSION['user_id'])) {
    header("Location: beranda.php");
    exit;
}

// Jika belum login → arahkan ke login
header("Location: login.php");
exit;
?>