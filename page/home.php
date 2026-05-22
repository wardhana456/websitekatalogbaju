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
  .product-card-link {
    display: block;
    color: #000 !important;
    text-decoration: none !important;
  }
  .product-card h5 {
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 15px;
  }
  .product-card img {
    transition: transform 0.3s ease;
  }
  .product-card-link:hover img {
    transform: scale(1.03);
  }
  .product-item small {
    color: #6c757d;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
  }

  /* --- SEKSI PRODUK UNIK --- */
  .unique-products-section {
    padding: 60px 0;
    background-color: #fff;
  }
  .unique-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111;
    margin-bottom: 8px;
  }
  .unique-subtitle {
    font-size: 0.9rem;
    color: #777;
    max-width: 600px;
    margin: 0 auto 40px auto;
    line-height: 1.6;
  }
  .unique-card {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 400px;
  }
  .unique-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .unique-card.featured {
    height: 450px; 
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    z-index: 2;
  }

  /* --- GLOBAL SETTINGS FASHION TALKS --- */
  .fashion-talks-section {
    padding: 100px 0;
    background-color: #ffffff;
    overflow: hidden;
  }

  /* Elemen Dekoratif Bintang */
  .decor-star {
    position: absolute;
    font-size: 2rem;
    font-weight: bold;
    user-select: none;
    z-index: 1;
  }
  .star-1 { top: 10%; right: 20%; color: #222; }
  .star-2 { bottom: 15%; left: 10%; color: #fff; text-shadow: 0 0 2px #000; }

  /* --- DETAIL SEKSI FASHION TALKS --- */
  .fashion-image-wrapper {
    position: relative;
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
    height: 400px;
  }
  .back-block-orange {
    position: absolute;
    top: 0;
    left: 0;
    width: 85%;
    height: 85%;
    background-color: #ff8e6e;
    z-index: 1;
  }
  .main-image-box {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 85%;
    height: 85%;
    z-index: 2;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  }
  .main-image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .decor-circle-blue {
    position: absolute;
    top: 5%;
    right: -25px;
    width: 100px;
    height: 100px;
    background-color: #6c7eff;
    border-radius: 50%;
    z-index: 3;
  }
  
  .fashion-text-card {
    background: #ffffff;
    padding: 45px;
    margin-left: -50px;
    position: relative;
    z-index: 4;
  }
  .fashion-text-card-left {
    background: #ffffff;
    padding: 45px;
    margin-right: -50px; 
    position: relative;
    z-index: 4;
  }
  .fashion-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: #111;
  }
  .fashion-desc {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.7;
    margin-bottom: 25px;
  }
  .btn-fashion-action {
    background-color: #ff6854;
    color: white !important;
    padding: 10px 30px;
    border-radius: 0px;
    font-weight: 600;
    font-size: 0.9rem;
  }

  /* --- RESPONSIVE BREAKPOINTS --- */
  @media (max-width: 767.98px) {
    .fashion-text-card {
      margin-left: 0;
      padding: 25px;
    }
    .fashion-text-card-left {
      margin-right: 0;
      padding: 25px;
    }
    .fashion-title {
      font-size: 1.8rem;
    }
    .fashion-image-wrapper {
      max-width: 320px;
      height: 320px;
    }
  }
</style>

<section class="unique-products-section text-center">
  <div class="container">
    <h2 class="unique-title">Produk Thrift Pilihan Kami</h2>
    <p class="unique-subtitle">
      Temukan koleksi kaos thrift berkualitas dengan gaya yang unik, trendi, dan penuh karakter.tampilan fashionable, serta harga yang ramah di kantong. Cocok untuk kamu yang ingin tampil keren, beda, dan tetap percaya diri dalam setiap aktivitas.
    </p>

    <div class="row align-items-center g-0 justify-content-center">
      <div class="col-4 col-md-3">
        <div class="unique-card">
          <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=600" alt="Unique Product 1">
        </div>
      </div>

      <div class="col-4 col-md-4">
        <div class="unique-card featured">
          <img src="https://images.unsplash.com/photo-1539109136881-3be0616acf4b?q=80&w=600" alt="Unique Product 2">
        </div>
      </div>

      <div class="col-4 col-md-3">
        <div class="unique-card">
          <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=600" alt="Unique Product 3">
        </div>
      </div>
    </div>
  </div>
</section>

<hr class="container my-5" style="border-top: 1px solid #eee;">

<section class="fashion-talks-section position-relative pt-4">
  <span class="decor-star star-1">✦</span>
  <span class="decor-star star-2">✦</span>

  <div class="container">
    <div class="row align-items-center position-relative">
      <div class="col-md-6 mb-5 mb-md-0">
        <div class="fashion-image-wrapper">
          <div class="back-block-orange"></div>
          <div class="decor-circle-blue"></div>
          <div class="main-image-box">
            <img src="foto/promosi1.jpg" alt="Fashion Talks">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="fashion-text-card shadow-sm">
          <h2 class="fashion-title">Let's Fashion Talks</h2>
          <p class="fashion-desc">
           Kaos adalah esensi gaya modern yang lahir pada tahun 2021 melalui rilisan Deccio Gucci, 
           menjadikannya salah satu pilar tren sandang lokal yang paling diminati saat ini. Sama seperti perjalanan mode pada umumnya, langkah awal 
           kami dimulai dari dedikasi menciptakan kenyamanan harian lewat selembar kain berkualitas.
          </p>
          <a href="#" class="btn btn-fashion-action">See All</a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="fashion-talks-section position-relative pt-0">
  <div class="container">
    <div class="row align-items-center position-relative flex-md-row-reverse">
      <div class="col-md-6 mb-5 mb-md-0">
        <div class="fashion-image-wrapper">
          <div class="back-block-orange"></div>
          <div class="decor-circle-blue"></div>
          <div class="main-image-box">
            <img src="foto/promosi1.jpg" alt="Fashion Talks">
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="fashion-text-card-left shadow-sm">
          <h2 class="fashion-title">Let's Fashion Talks</h2>
          <p class="fashion-desc">
            Kaos adalah esensi gaya modern yang lahir pada tahun 2021 melalui rilisan Deccio Gucci, 
            menjadikannya salah satu pilar tren sandang lokal yang paling diminati saat ini. Sama seperti perjalanan mode pada umumnya, langkah awal 
            kami dimulai dari dedikasi menciptakan kenyamanan harian lewat selembar kain berkualitas.
          </p>
          <a href="#" class="btn btn-fashion-action">See All</a>
        </div>
      </div>
    </div>
  </div>
</section>

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

          <!-- Setiap card produk dibungkus Form POST agar tombol berfungsi independen -->
          <form action="" method="POST">
            <!-- Hidden input untuk mengirim ID produk yang dipilih -->
            <input type="hidden" name="produk_id" value="<?= $p['produk_id'] ?>">

            <a href="detail_produk.php?id=<?= $p['produk_id'] ?>" class="product-card-link">
              <img src="<?= htmlspecialchars($p['gambar_url']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" loading="lazy">
              <h5><?= htmlspecialchars($p['nama_produk']) ?></h5>
            </a>

            <p class="mb-1">
              Rp <?= number_format($p['harga'], 0, ',', '.') ?>
            </p>

            <small class="d-block mb-3">
              <?= htmlspecialchars($p['nama_kategori'] ?? 'Uncategorized') ?>
            </small>

            <!-- Tipe tombol diubah ke submit dan ditambahkan atribut name -->
            <button type="submit" name="add_to_cart_direct" class="btn btn-dark w-100 py-2">
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
      filterButtons.forEach(btn => btn.classList.remove('active'));
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