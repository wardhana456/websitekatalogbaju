<?php
session_start();
include_once("../config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $query = $conn->query("SELECT * FROM user WHERE email = '$email'");

    if ($query->num_rows === 0) {
        $_SESSION['login_error'] = "Akun tidak ditemukan. Silakan daftar terlebih dahulu!";
        header("Location: ../login.php");
        exit;
    }

    $user = $query->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        $_SESSION['login_error'] = "Password salah!";
        header("Location: ../login.php");
        exit;
    }

    // LOGIN BERHASIL
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

    // REDIRECT
    if ($user['role'] === 'admin') {
        header("Location: ../dashboard-admin/admin-page/dashboard.php");
    } else {
        header("Location: ../beranda.php"); // ✔️ ini yang kamu mau
    }
    exit;
}
?>