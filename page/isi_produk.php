<?php
// shop.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once(__DIR__ . "/../config/database.php");

/* ========================================================
   LOGIC: Proses Tambah ke Keranjang Langsung via Shop
======================================================== */
if (isset($_POST['add_to_cart_direct'])) {
    // PROTEKSI: User wajib login terlebih dahulu
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Silakan login terlebih dahulu untuk belanja!'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $produk_id = intval($_POST['produk_id']);
    $qty = 1; // Default kuantitas dari halaman katalog/shop adalah 1

    // Ambil harga asli produk dari database untuk kalkulasi subtotal (Proteksi Harga)
    $getHarga = mysqli_prepare($conn, "SELECT harga FROM produk WHERE produk_id = ?");
    mysqli_stmt_bind_param($getHarga, "i", $produk_id);
    mysqli_stmt_execute($getHarga);
    $resHarga = mysqli_stmt_get_result($getHarga);
    
    if ($rowH = mysqli_fetch_assoc($resHarga)) {
        $harga = floatval($rowH['harga']);
        $subtotal = $harga * $qty;

        // A. Cek atau Buat Keranjang Aktif User
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

        // B. Cek apakah produk tersebut sudah terdaftar di cart_detail
        $queryDetail = mysqli_prepare($conn, "SELECT cart_detail_id, kuantitas FROM cart_detail WHERE cart_id = ? AND produk_id = ? LIMIT 1");
        mysqli_stmt_bind_param($queryDetail, "ii", $cart_id, $produk_id);
        mysqli_stmt_execute($queryDetail);
        $resultDetail = mysqli_stmt_get_result($queryDetail);
        $detail = mysqli_fetch_assoc($resultDetail);

        if ($detail) {
            // Jika sudah ada, akumulasikan kuantitas + 1
            $new_qty = $detail['kuantitas'] + $qty;
            $new_subtotal = $harga * $new_qty;
            
            $updateDetail = mysqli_prepare($conn, "UPDATE cart_detail SET kuantitas = ?, subtotal = ? WHERE cart_detail_id = ?");
            mysqli_stmt_bind_param($updateDetail, "idi", $new_qty, $new_subtotal, $detail['cart_detail_id']);
            mysqli_stmt_execute($updateDetail);
        } else {
            // Jika belum ada, buat rekam item baru
            $insertDetail = mysqli_prepare($conn, "INSERT INTO cart_detail (cart_id, produk_id, kuantitas, subtotal) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insertDetail, "iiid", $cart_id, $produk_id, $qty, $subtotal);
            mysqli_stmt_execute($insertDetail);
        }

        echo "<script>alert('Produk berhasil dimasukkan ke keranjang database!'); window.location.href='keranjang.php';</script>";
        exit;
    }
}

/* =========================
   AMBIL DATA KATEGORI
========================= */
$queryKategori = mysqli_query($conn, "SELECT * FROM kategori");

/* =========================
   AMBIL DATA PRODUK
========================= */
$queryProduk = mysqli_query($conn, "
    SELECT produk.*, kategori.nama_kategori
    FROM produk
    LEFT JOIN kategori
    ON produk.kategori_id = kategori.kategori_id
");
?>

<style>
.product-card {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
    transition: .3s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-card form {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card-link {
    text-decoration: none;
    color: inherit;
}

.product-img {
    width: 100%;
    height: 250px; /* Tinggi semua gambar sama */
    object-fit: cover; /* Crop otomatis */
    border-radius: 10px;
    margin-bottom: 12px;
}

.product-card h5 {
    min-height: 50px; /* Tinggi judul sama */
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
    overflow: hidden;
}

.product-card p {
    font-weight: bold;
    color: #000;
}

.product-card button {
    margin-top: auto;
}

/* Tablet */
@media (max-width: 992px) {
    .product-img {
        height: 220px;
    }
}

/* Mobile */
@media (max-width: 576px) {
    .product-img {
        height: 200px;
    }
}
  }
</style>

<section class="shop-header reveal">
  <h1>Our Collections</h1>
  <p>Temukan pakaian thrift berkualitas dengan harga terbaik.</p>
</section>

<div class="filters reveal">
  <button type="button" class="filter-btn active" data-filter="semua">
    Semua
  </button>

  <?php while ($k = mysqli_fetch_assoc($queryKategori)): ?>
    <button type="button" class="filter-btn" data-filter="<?= strtolower($k['nama_kategori']) ?>">
      <?= htmlspecialchars($k['nama_kategori']) ?>
    </button>
  <?php endwhile; ?>
</div>

<section class="container mt-4 mb-5 reveal">

  <div class="text-center mb-4">
    <h3 class="section-title">
      <strong>TRENDING</strong>
    </h3>
    <p class="subtitle">
      Koleksi terpilih untuk kamu
    </p>
  </div>

  <div class="row g-4" id="product-list">
    <?php while ($p = mysqli_fetch_assoc($queryProduk)): ?>
      
      <div class="col-12 col-sm-6 col-lg-3 product-item" data-category="<?= strtolower($p['nama_kategori'] ?? '') ?>">
        <div class="product-card">

          <form action="" method="POST">
            <input type="hidden" name="produk_id" value="<?= $p['produk_id'] ?>">

            <a href="detail_produk.php?id=<?= $p['produk_id'] ?>" class="product-card-link">
              <img class="product-img"
                   src="<?= htmlspecialchars($p['gambar_url']) ?>"
                   alt="<?= htmlspecialchars($p['nama_produk']) ?>"
                   loading="lazy">

              <h5><?= htmlspecialchars($p['nama_produk']) ?></h5>
            </a>

            <p class="mb-1">
              Rp <?= number_format($p['harga'], 0, ',', '.') ?>
            </p>

            <small class="d-block mb-3">
              <?= htmlspecialchars($p['nama_kategori'] ?? 'Uncategorized') ?>
            </small>

            <button type="submit" name="add_to_cart_direct" class="btn btn-dark w-100 py-2 mt-auto">
              <i class="bi bi-cart-plus me-2"></i> Tambah ke Keranjang
            </button>
          </form>

        </div>
      </div>

    <?php endwhile; ?>
</div>

</section>

<script>
  const filterButtons = document.querySelectorAll('.filter-btn');
  const products = document.querySelectorAll('.product-item');

  filterButtons.forEach(button => {
    button.addEventListener('click', () => {
      // hapus active
      filterButtons.forEach(btn => btn.classList.remove('active'));

      // tambah active
      button.classList.add('active');

      const filter = button.dataset.filter;

      products.forEach(product => {
        const category = product.dataset.category;

        if (filter === 'semua' || filter === category) {
          product.style.display = 'block';
        } else {
          product.style.display = 'none';
        }
      });
    });
  });
</script>