<?php
// checkout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("config/database.php");

// Proteksi: User wajib login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = intval($_SESSION['user_id']);

/* =====================================
   1. AMBIL BARANG DARI KERANJANG AKTIF
===================================== */
$cart_items = [];
$subtotal = 0;

$queryCart = mysqli_prepare($conn, "
    SELECT cd.*, p.nama_produk, p.harga, p.gambar_url
    FROM cart_detail cd
    JOIN cart c ON cd.cart_id = c.cart_id
    JOIN produk p ON cd.produk_id = p.produk_id
    WHERE c.user_id = ? AND c.status = 'aktif'
");
mysqli_stmt_bind_param($queryCart, "i", $user_id);
mysqli_stmt_execute($queryCart);
$resultCart = mysqli_stmt_get_result($queryCart);

while ($row = mysqli_fetch_assoc($resultCart)) {
    $subtotal += $row['subtotal'];
    $cart_items[] = $row;
}

// Jika isi keranjang kosong, kembalikan ke halaman keranjang
if (empty($cart_items)) {
    header("Location: keranjang.php");
    exit;
}

// Perhitungan Final (Disamakan dengan keranjang.php)
$shipping = 0; 
$postage = 24000; 
$total_akhir = $subtotal + $shipping + $postage;

/* =====================================
   2. PROSES PEMBUATAN ORDER (AKSI POST)
===================================== */
if (isset($_POST['place_order'])) {
    
    // Mulai Database Transaction agar data aman dan konsisten jika terjadi error di tengah jalan
    mysqli_begin_transaction($conn);

    try {
        // A. Insert ke TABLE orders
        $insertOrder = mysqli_prepare($conn, "
            INSERT INTO orders (user_id, total_harga, status) 
            VALUES (?, ?, 'pending')
        ");
        mysqli_stmt_bind_param($insertOrder, "id", $user_id, $total_akhir);
        mysqli_stmt_execute($insertOrder);
        $order_id = mysqli_insert_id($conn);

        // B. Ambil ID Cart Aktif untuk pemindahan data & update status
        $cart_id = $cart_items[0]['cart_id'];

        // C. Loop & Pindahkan item ke TABLE order_detail
        $insertDetail = mysqli_prepare($conn, "
            INSERT INTO order_detail (order_id, produk_id, kuantitas, subtotal) 
            VALUES (?, ?, ?, ?)
        ");

        foreach ($cart_items as $item) {
            mysqli_stmt_bind_param($insertDetail, "iiid", 
                $order_id, 
                $item['produk_id'], 
                $item['kuantitas'], 
                $item['subtotal']
            );
            mysqli_stmt_execute($insertDetail);
        }

        // D. Update status TABLE cart menjadi 'checkout' agar keranjang kosong kembali
        $updateCart = mysqli_prepare($conn, "
            UPDATE cart SET status = 'checkout' WHERE cart_id = ?
        ");
        mysqli_stmt_bind_param($updateCart, "i", $cart_id);
        mysqli_stmt_execute($updateCart);

        // Jika semua query sukses, commit perubahan ke database
        mysqli_commit($conn);

        // Alihkan ke halaman konfirmasi sukses pesanan dengan membawa order_id
        echo "<script>
                alert('Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
                window.location.href = 'proses/pesanan_sukses.php?id=" . $order_id . "';
              </script>";
        exit;

    } catch (Exception $e) {
        // Jika ada kegagalan query, batalkan semua perubahan di database
        mysqli_rollback($conn);
        echo "<script>alert('Gagal memproses pesanan, silakan coba lagi.');</script>";
    }
}
?>

<?php include("component/header.php"); ?>

  <style>
    body {
      background-color: #fff;
      color: #000;
      font-family: Arial, sans-serif;
      padding-top: 90px;
    }
    .checkout-title {
      font-size: 2.2rem;
      font-weight: 800;
      letter-spacing: -1px;
      text-transform: uppercase;
      border-bottom: 2px solid #000;
      padding-bottom: 15px;
    }
    .section-sub-title {
      font-size: 1.1rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid #000;
      padding-bottom: 8px;
    }
    .form-thrift input, .form-thrift select {
      border-radius: 0;
      border: 1px solid #000;
      padding: 10px;
    }
    .form-thrift input:focus, .form-thrift select:focus {
      box-shadow: none;
      border-color: #e94560;
    }
    .order-summary-box {
      border: 1px solid #000;
      padding: 25px;
      background-color: #fafafa;
    }
    .checkout-item-row {
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
      border-bottom: 1px dashed #ccc;
      padding: 10px 0;
    }
    .summary-line {
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
      margin-top: 10px;
    }
    .summary-total {
      font-weight: 800;
      font-size: 1.1rem;
      border-top: 1px solid #000;
      padding-top: 15px;
      margin-top: 15px;
    }
    .btn-place-order {
      background-color: #000;
      color: #fff;
      border: 1px solid #000;
      width: 100%;
      padding: 14px;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.9rem;
      letter-spacing: 1px;
      transition: 0.2s;
      margin-top: 20px;
    }
    .btn-place-order:hover {
      background-color: #fff;
      color: #000;
    }
  </style>
</head>
<body>

<?php include("component/navbar.php"); ?>

<div class="container my-5">
  <h1 class="checkout-title mb-5">Checkout</h1>

  <form action="" method="POST" class="form-thrift">
    <div class="row g-5">
      
      <div class="col-lg-7">
        <h4 class="section-sub-title mb-4">Informasi Pengiriman</h4>
        
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold small">Nama Lengkap Penerima</label>
            <input type="text" class="form-control" placeholder="Contoh: Travis Scott" required>
          </div>
          
          <div class="col-md-6">
            <label class="form-label fw-semibold small">Nomor Telepon / WhatsApp</label>
            <input type="tel" class="form-control" placeholder="Contoh: 08123456xxx" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold small">Metode Pembayaran</label>
            <select class="form-select" required>
              <option value="qris" selected>QRIS / E-Wallet (Otomatis)</option>
              <option value="transfer">Bank Transfer (Manual)</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold small">Alamat Lengkap</label>
            <textarea class="form-control" rows="4" style="border-radius:0; border: 1px solid #000;" placeholder="Nama jalan, nomor rumah, RT/RW, Kecamatan, Kota, dan Kode Pos" required></textarea>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="order-summary-box">
          <h4 class="text-center fw-bold mb-4" style="letter-spacing: 1px; text-transform:uppercase; font-size:1.1rem;">Review Your Order</h4>
          
          <div class="mb-4" style="max-height: 240px; overflow-y: auto; padding-right: 5px;">
            <?php foreach ($cart_items as $item): ?>
              <div class="checkout-item-row">
                <span class="text-truncate" style="max-width: 250px;">
                  <?= htmlspecialchars($item['nama_produk']) ?> 
                  <strong class="text-muted">x<?= $item['kuantitas'] ?></strong>
                </span>
                <span class="fw-semibold">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="summary-line">
            <span class="text-muted">Subtotal Produk</span>
            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
          </div>
          <div class="summary-line">
            <span class="text-muted">Biaya Pengiriman</span>
            <span>Free</span>
          </div>
          <div class="summary-line">
            <span class="text-muted">Penanganan & Ongkir Flat</span>
            <span>Rp <?= number_format($postage, 0, ',', '.') ?></span>
          </div>

          <div class="summary-line summary-total">
            <span>TOTAL TAGIHAN</span>
            <span style="color: #e94560;">Rp <?= number_format($total_akhir, 0, ',', '.') ?></span>
          </div>

          <button type="submit" name="place_order" class="btn-place-order">
            Place Order <i class="bi bi-arrow-right-short ms-1"></i>
          </button>
        </div>
      </div>

    </div>
  </form>
</div>

</body>
</html>