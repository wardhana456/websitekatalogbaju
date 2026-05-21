<?php
session_start();
include_once("config/database.php");

$nama     = $conn->real_escape_string($_POST['nama']);
$email    = $conn->real_escape_string($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$alamat   = $conn->real_escape_string($_POST['alamat']);
$no_hp    = $conn->real_escape_string($_POST['no_hp']);

// CEK EMAIL
$cek = $conn->query("SELECT email FROM user WHERE email='$email'");

if ($cek->num_rows > 0) {
    $_SESSION['register_error'] = "Email sudah terdaftar!";
    header("Location: register.php");
    exit;
}

// INSERT USER
$conn->query("
    INSERT INTO user (nama, email, password, alamat, no_hp, role)
    VALUES ('$nama','$email','$password','$alamat','$no_hp','user')
");

$_SESSION['register_success'] = "Akun berhasil dibuat, silakan login!";
header("Location: login.php");
exit;
?>