<?php
// detail_produk.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("config/database.php");

// 1. Ambil ID Produk dari URL
$produk_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Query ambil detail produk beserta kategorinya
$query = mysqli_prepare($conn, "
    SELECT produk.*, kategori.nama_kategori 
    FROM produk 
    LEFT JOIN kategori ON produk.kategori_id = kategori.kategori_id 
    WHERE produk.produk_id = ?
");
mysqli_stmt_bind_param($query, "i", $produk_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$produk = mysqli_fetch_assoc($result);

// Jika produk tidak ditemukan, alihkan ke halaman shop
if (!$produk) {
    header("Location: shop.php");
    exit;
}

// 3. LOGIC: Proses Tambah ke Keranjang Berbasis Database
if (isset($_POST['add_to_cart'])) {
    // PROTEKSI: User wajib login untuk menambah ke keranjang database
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Silakan login terlebih dahulu untuk belanja!'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if ($qty < 1) $qty = 1;
    
    $harga = floatval($produk['harga']);
    $subtotal = $harga * $qty;

    // A. Cek apakah user sudah punya cart berstatus 'aktif'
    $queryCart = mysqli_prepare($conn, "SELECT cart_id FROM cart WHERE user_id = ? AND status = 'aktif' LIMIT 1");
    mysqli_stmt_bind_param($queryCart, "i", $user_id);
    mysqli_stmt_execute($queryCart);
    $resultCart = mysqli_stmt_get_result($queryCart);
    $cart = mysqli_fetch_assoc($resultCart);

    if ($cart) {
        $cart_id = $cart['cart_id'];
    } else {
        // Jika belum punya cart aktif, buat baru di tabel cart
        $insertCart = mysqli_prepare($conn, "INSERT INTO cart (user_id, status) VALUES (?, 'aktif')");
        mysqli_stmt_bind_param($insertCart, "i", $user_id);
        mysqli_stmt_execute($insertCart);
        $cart_id = mysqli_insert_id($conn);
    }

    // B. Cek apakah produk tersebut sudah ada di dalam cart_detail
    $queryDetail = mysqli_prepare($conn, "SELECT cart_detail_id, kuantitas FROM cart_detail WHERE cart_id = ? AND produk_id = ? LIMIT 1");
    mysqli_stmt_bind_param($queryDetail, "ii", $cart_id, $produk_id);
    mysqli_stmt_execute($queryDetail);
    $resultDetail = mysqli_stmt_get_result($queryDetail);
    $detail = mysqli_fetch_assoc($resultDetail);

    if ($detail) {
        // Jika produk sudah ada, akumulasikan jumlah kuantitas dan update subtotalnya
        $new_qty = $detail['kuantitas'] + $qty;
        $new_subtotal = $harga * $new_qty;
        
        $updateDetail = mysqli_prepare($conn, "UPDATE cart_detail SET kuantitas = ?, subtotal = ? WHERE cart_detail_id = ?");
        mysqli_stmt_bind_param($updateDetail, "idi", $new_qty, $new_subtotal, $detail['cart_detail_id']);
        mysqli_stmt_execute($updateDetail);
    } else {
        // Jika belum ada, masukkan item baru ke cart_detail
        $insertDetail = mysqli_prepare($conn, "INSERT INTO cart_detail (cart_id, produk_id, kuantitas, subtotal) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($insertDetail, "iiid", $cart_id, $produk_id, $qty, $subtotal);
        mysqli_stmt_execute($insertDetail);
    }

    echo "<script>alert('Produk berhasil dimasukkan ke keranjang database!'); window.location.href='keranjang.php';</script>";
    exit;
}
?>

<?php include("component/header.php"); ?>

  <style>
    body { background-color: #f8f9fa; padding-top: 90px; }
    .breadcrumb-thrift { font-size: 0.85rem; color: #6c757d; }
    .product-main-img { width: 100%; max-height: 500px; object-fit: cover; border-radius: 8px; background-color: #fff; }
    .product-title { font-size: 2rem; font-weight: 700; letter-spacing: -0.5px; text-transform: uppercase; }
    .product-price { font-size: 1.5rem; font-weight: 600; color: #e94560; }
    .btn-thrift-dark { background-color: #111; color: #fff; border: none; padding: 12px 30px; font-weight: 600; text-transform: uppercase; transition: 0.2s; }
    .btn-thrift-dark:hover { background-color: #e94560; color: #fff; }
    .quantity-control input { width: 60px; text-align: center; border: 1px solid #ced4da; }
  </style>
</head>
<body>

<?php include("component/navbar.php"); ?>

<div class="container my-5">

  <div class="row g-5">
    <div class="col-md-6">
      <img src="<?= htmlspecialchars($produk['gambar_url']) ?>" class="product-main-img shadow-sm" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
    </div>

    <div class="col-md-6 d-flex flex-column justify-content-center">
      <h1 class="product-title mb-2"><?= htmlspecialchars($produk['nama_produk']) ?></h1>
      <p class="product-price mb-4">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
      <hr>
      <p class="text-muted my-3">
        <?= nl2br(htmlspecialchars($produk['deskripsi'] ?? 'Tidak ada deskripsi untuk produk ini.')) ?>
      </p>
      <hr class="mb-4">

      <form action="" method="POST">
        <div class="d-flex align-items-center gap-3 mb-4">
          <label class="fw-semibold">Jumlah:</label>
          <div class="input-group quantity-control" style="width: 130px;">
            <button class="btn btn-outline-secondary" type="button" onclick="changeQty(-1)">-</button>
            <input type="number" name="quantity" id="quantity" class="form-control bg-white" value="1" min="1" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="changeQty(1)">+</button>
          </div>
        </div>

        <button type="submit" name="add_to_cart" class="btn btn-thrift-dark w-100 py-3">
          <i class="bi bi-bag-plus me-2"></i> Tambah ke Keranjang
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  function changeQty(amount) {
    const qtyInput = document.getElementById('quantity');
    let currentQty = parseInt(qtyInput.value);
    currentQty += amount;
    if (currentQty < 1) currentQty = 1;
    qtyInput.value = currentQty;
  }
</script>

</body>
</html>