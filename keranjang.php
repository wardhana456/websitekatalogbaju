<?php
// keranjang.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("config/database.php");

// PROTEKSI: Wajib login untuk melihat keranjang database
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login untuk melihat keranjang belanja Anda.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = intval($_SESSION['user_id']);

/* =====================================
   LOGIC: Hapus Item dari Cart Detail
===================================== */
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $detail_id = intval($_GET['id']);
    
    // Validasi tambahan agar user tidak bisa sembarangan menghapus cart orang lain
    $deleteQuery = mysqli_prepare($conn, "
        DELETE cd FROM cart_detail cd
        JOIN cart c ON cd.cart_id = c.cart_id
        WHERE cd.cart_detail_id = ? AND c.user_id = ? AND c.status = 'aktif'
    ");
    mysqli_stmt_bind_param($deleteQuery, "ii", $detail_id, $user_id);
    mysqli_stmt_execute($deleteQuery);

    header("Location: keranjang.php");
    exit;
}

/* =====================================
   AMBIL DATA KERANJANG DARI DATABASE
===================================== */
$cart_items = [];
$subtotal = 0;

$query = mysqli_prepare($conn, "
    SELECT 
        cd.cart_detail_id, 
        cd.kuantitas, 
        cd.subtotal AS total_item,
        p.produk_id, 
        p.nama_produk, 
        p.harga, 
        p.gambar_url
    FROM cart_detail cd
    JOIN cart c ON cd.cart_id = c.cart_id
    JOIN produk p ON cd.produk_id = p.produk_id
    WHERE c.user_id = ? AND c.status = 'aktif'
");
mysqli_stmt_bind_param($query, "i", $user_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

while ($row = mysqli_fetch_assoc($result)) {
    $subtotal += $row['total_item'];
    $cart_items[] = $row;
}

// Konfigurasi Tambahan Ringkasan Biaya (Elegan Minimalis)
$shipping = 0; // Free ongkir flat
$postage = $subtotal > 0 ? 24000 : 0; // Ongkir jika keranjang berisi item
$total_akhir = $subtotal + $shipping + $postage;
?>

<?php include("component/header.php"); ?>
 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #fff; color: #000; font-family: Arial, sans-serif; padding-top: 90px; }
    .cart-title { font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 15px; }
    .cart-item-row { border-bottom: 1px solid #e5e5e5; padding: 20px 0; }
    .cart-img { width: 100px; height: 120px; object-fit: cover; background-color: #f5f5f5; }
    .item-title { font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-box { border: 1px solid #000; padding: 20px; }
    .summary-title { font-size: 1.2rem; font-weight: 800; text-transform: uppercase; border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 15px; text-align: center; }
    .summary-line { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 10px; }
    .summary-total { font-weight: 800; border-top: 1px solid #000; padding-top: 10px; margin-top: 15px; }
    .btn-checkout { background-color: #000; color: #fff; border: 1px solid #000; width: 100%; padding: 12px; font-weight: 700; text-transform: uppercase; font-size: 0.85rem; transition: 0.2s; margin-top: 15px; }
    .btn-checkout:hover { background-color: #fff; color: #000; }
  </style>
</head>
<body>

<?php include("component/navbar.php"); ?>

<div class="container my-5">
  <h1 class="cart-title mb-5 text-center text-md-start">Shopping Cart</h1>

  <?php if (empty($cart_items)): ?>
    <div class="text-center my-5 py-5">
      <i class="bi bi-bag-x" style="font-size: 4rem; color: #ccc;"></i>
      <h3 class="mt-3">Keranjang Belanja Kamu Kosong</h3>
      <p class="text-muted">Yuk, cari pakaian thrift keren di katalog kami!</p>
      <a href="shop.php" class="btn btn-dark mt-2 px-4 rounded-0">Mulai Belanja</a>
    </div>
  <?php else: ?>

    <div class="row g-5">
      <div class="col-lg-8">
        <?php foreach ($cart_items as $item): ?>
          <div class="row cart-item-row align-items-center">
            <div class="col-3 col-sm-2">
              <img src="<?= htmlspecialchars($item['gambar_url']) ?>" class="cart-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
            </div>
            <div class="col-7 col-sm-8 ps-3 ps-sm-4">
              <h5 class="item-title mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h5>
              <p class="mb-1 fw-semibold text-muted">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
              <div class="text-muted font-monospace" style="font-size: 0.85rem;">
                Jumlah: <strong><?= $item['kuantitas'] ?></strong>
              </div>
            </div>
            <div class="col-2 text-end d-flex flex-column justify-content-between align-items-end" style="height: 90px;">
              <a href="keranjang.php?action=delete&id=<?= $item['cart_detail_id'] ?>" class="text-danger" onclick="return confirm('Hapus item ini dari keranjang?')" title="Hapus Item">
                <i class="bi bi-trash3 fs-5"></i>
              </a>
              <span class="fw-bold text-dark">Rp <?= number_format($item['total_item'], 0, ',', '.') ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="col-lg-4">
        <div class="summary-box">
          <div class="summary-title">Order Summary</div>
          
          <div class="summary-line">
            <span>Subtotal</span>
            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
          </div>
          
          <div class="summary-line">
            <span>Shipping</span>
            <span>Free</span>
          </div>

          <div class="summary-line">
            <span>Postage</span>
            <span>Rp <?= number_format($postage, 0, ',', '.') ?></span>
          </div>

          <div class="summary-line summary-total">
            <span>TOTAL</span>
            <span>Rp <?= number_format($total_akhir, 0, ',', '.') ?></span>
          </div>

          <button type="button" class="btn-checkout" onclick="window.location.href='checkout.php'">
            Check Out
          </button>
        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

</body>
</html>