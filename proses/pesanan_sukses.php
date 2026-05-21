<?php
// pesanan_sukses.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Order Success - ThriftPay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="text-center p-5 bg-white shadow-sm border" style="max-width: 500px;">
    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
    <h2 class="fw-bold mt-3">ORDER BERHASIL!</h2>
    <p class="text-muted">Terima kasih telah berbelanja di ThriftPay. ID Pesanan kamu adalah <strong>#<?= $order_id ?></strong>.</p>
    <p class="small text-secondary">Silakan periksa halaman <strong>Pesanan Saya</strong> pada menu dropdown avatar kamu untuk melihat status pengiriman.</p>
    <a href="../beranda.php" class="btn btn-dark rounded-0 px-4 mt-3">Kembali ke Beranda</a>
  </div>
</body>
</html>