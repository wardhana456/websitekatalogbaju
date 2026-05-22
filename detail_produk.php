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

// ========================================================
// LOGIC A: Proses Tambah ke Keranjang Berbasis Database
// ========================================================
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

    // Cek apakah user sudah punya cart berstatus 'aktif'
    $queryCart = mysqli_prepare($conn, "SELECT cart_id FROM cart WHERE user_id = ? AND status = 'aktif' LIMIT 1");
    mysqli_stmt_bind_param($queryCart, "i", $user_id);
    mysqli_stmt_execute($queryCart);
    $resultCart = mysqli_stmt_get_result($queryCart);
    $cart = mysqli_fetch_assoc($resultCart);

    if ($cart) {
        $cart_id = $cart['cart_id'];
    } else {
        $insertCart = mysqli_prepare($conn, "INSERT INTO cart (user_id, status) VALUES (?, 'aktif')");
        mysqli_stmt_bind_param($insertCart, "i", $user_id);
        mysqli_stmt_execute($insertCart);
        $cart_id = mysqli_insert_id($conn);
    }

    // Cek apakah produk tersebut sudah ada di dalam cart_detail
    $queryDetail = mysqli_prepare($conn, "SELECT cart_detail_id, kuantitas FROM cart_detail WHERE cart_id = ? AND produk_id = ? LIMIT 1");
    mysqli_stmt_bind_param($queryDetail, "ii", $cart_id, $produk_id);
    mysqli_stmt_execute($queryDetail);
    $resultDetail = mysqli_stmt_get_result($queryDetail);
    $detail = mysqli_fetch_assoc($resultDetail);

    if ($detail) {
        $new_qty = $detail['kuantitas'] + $qty;
        $new_subtotal = $harga * $new_qty;
        
        $updateDetail = mysqli_prepare($conn, "UPDATE cart_detail SET kuantitas = ?, subtotal = ? WHERE cart_detail_id = ?");
        $updateDetail->bind_param("idi", $new_qty, $new_subtotal, $detail['cart_detail_id']);
        mysqli_stmt_execute($updateDetail);
    } else {
        $insertDetail = mysqli_prepare($conn, "INSERT INTO cart_detail (cart_id, produk_id, kuantitas, subtotal) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($insertDetail, "iiid", $cart_id, $produk_id, $qty, $subtotal);
        mysqli_stmt_execute($insertDetail);
    }

    echo "<script>alert('Produk berhasil dimasukkan ke keranjang database!'); window.location.href='keranjang.php';</script>";
    exit;
}

// ========================================================
// LOGIC B: Proses Simpan Review Baru
// ========================================================
if (isset($_POST['kirim_review'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Silakan login terlebih dahulu untuk memberikan ulasan!'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $komentar = trim($_POST['komentar']);

    // Validasi input rating batas minimal 1 dan maksimal 5
    if ($rating < 1 || $rating > 5) $rating = 5;

    if (!empty($komentar)) {
        $insertReview = mysqli_prepare($conn, "INSERT INTO review (user_id, produk_id, rating, komentar) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($insertReview, "iiis", $user_id, $produk_id, $rating, $komentar);
        
        if (mysqli_stmt_execute($insertReview)) {
            echo "<script>alert('Terima kasih! Ulasan Anda berhasil disimpan.'); window.location.href='detail_produk.php?id=$produk_id';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal mengirim ulasan, silakan coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Kolom komentar tidak boleh kosong!');</script>";
    }
}

// ========================================================
// LOGIC C: Ambil Semua Review untuk Produk Ini
// ========================================================
$queryAllReview = mysqli_prepare($conn, "
    SELECT r.*, u.nama 
    FROM review r 
    JOIN user u ON r.user_id = u.user_id 
    WHERE r.produk_id = ? 
    ORDER BY r.tanggal_review DESC
");
mysqli_stmt_bind_param($queryAllReview, "i", $produk_id);
mysqli_stmt_execute($queryAllReview);
$reviews = mysqli_stmt_get_result($queryAllReview);
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
    
    /* Tambahan Styling Section Review */
    .review-box { background: #ffffff; border-radius: 8px; padding: 20px; border: 1px solid #e3e6f0; }
    .star-rating-display { color: #ffc107; font-size: 0.9rem; }
    .review-avatar { width: 45px; height: 45px; background-color: #e94560; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; border-radius: 50%; text-transform: uppercase; }
  </style>
</head>
<body>

<?php include("component/navbar.php"); ?>

<div class="container my-5">
  <!-- Detail Produk Atas -->
  <div class="row g-5 mb-5">
    <div class="col-md-6">
      <img src="<?= htmlspecialchars($produk['gambar_url'] ?? 'assets/img/no-image.jpg') ?>" class="product-main-img shadow-sm" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
    </div>

    <div class="col-md-6 d-flex flex-column justify-content-center">
      <div class="mb-2">
        <span class="badge bg-secondary font-monospace text-uppercase"><?= htmlspecialchars($produk['nama_kategori'] ?? 'Uncategorized') ?></span>
      </div>
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

  <!-- ======================================================== -->
  <!-- SECTION BARU: REVIEWS & KOMENTAR                        -->
  <!-- ======================================================== -->
  <div class="row pt-4 border-top">
    <div class="col-lg-8 mx-auto">
      
      <!-- Form Input Review Baru -->
      <div class="review-box mb-4 shadow-sm">
        <h5 class="fw-bold mb-3"><i class="bi bi-chat-left-heart-fill text-danger me-2"></i>Tulis Ulasan Produk</h5>
        
        <?php if (isset($_SESSION['user_id'])): ?>
          <form action="" method="POST">
            <div class="mb-3">
              <label class="form-label fw-semibold">Beri Rating Bintang:</label>
              <select name="rating" class="form-select" style="width: 180px;" required>
                <option value="5">⭐⭐⭐⭐⭐ (5 / Sempurna)</option>
                <option value="4">⭐⭐⭐⭐ (4 / Bagus)</option>
                <option value="3">⭐⭐⭐ (3 / Cukup)</option>
                <option value="2">⭐⭐ (2 / Kurang)</option>
                <option value="1">⭐ (1 / Buruk)</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Tulis Komentar / Review Anda:</label>
              <textarea name="komentar" rows="3" class="form-control" placeholder="Ceritakan bagaimana kualitas bahan pakaian, ukuran, atau kecocokan produk ini..." required></textarea>
            </div>
            <button type="submit" name="kirim_review" class="btn btn-dark px-4 font-monospace">
              <i class="bi bi-send-fill me-2"></i>Kirim Ulasan
            </button>
          </form>
        <?php else: ?>
          <div class="alert alert-warning m-0 text-center" role="alert">
            <i class="bi bi-lock-fill me-2"></i>Anda harus login terlebih dahulu untuk menulis ulasan. 
            <a href="login.php" class="alert-link text-decoration-underline">Klik disini untuk Login</a>.
          </div>
        <?php endif; ?>
      </div>

      <!-- List Daftar Review yang Sudah Ada -->
      <h5 class="fw-bold mb-3"><i class="bi bi-people-fill me-2"></i>Ulasan Pelanggan (<?= mysqli_num_rows($reviews) ?>)</h5>
      
      <div class="d-flex flex-column gap-3">
        <?php if (mysqli_num_rows($reviews) > 0): ?>
          <?php while ($rowReview = mysqli_fetch_assoc($reviews)): ?>
            <div class="card p-3 shadow-sm border-0">
              <div class="d-flex align-items-start gap-3">
                
                <!-- Avatar inisial nama -->
                <div class="review-avatar flex-shrink-0">
                  <?= substr(htmlspecialchars($rowReview['nama']), 0, 1) ?>
                </div>
                
                <!-- Isi teks ulasan -->
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold m-0 text-dark"><?= htmlspecialchars($rowReview['nama']) ?></h6>
                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                      <?= date('d M Y - H:i', strtotime($rowReview['tanggal_review'])) ?>
                    </small>
                  </div>
                  
                  <!-- Menampilkan bintang dinamis -->
                  <div class="star-rating-display mb-2">
                    <?php 
                    $stars = intval($rowReview['rating']);
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $stars ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                    }
                    ?>
                  </div>
                  
                  <p class="text-secondary m-0" style="font-size: 0.9rem; line-height: 1.5;">
                    <?= nl2br(htmlspecialchars($rowReview['komentar'])) ?>
                  </p>
                </div>
                
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="text-center py-4 text-muted bg-white rounded border border-light">
            <i class="bi bi-chat-square-text d-block fs-2 mb-2 text-secondary"></i>
            <span class="small font-monospace">Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan review!</span>
          </div>
        <?php endif; ?>
      </div>

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