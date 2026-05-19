<?php /* ===== page/isi_produk.php — Konten halaman Produk / Shop Collection ===== */ ?>

<!-- ========== SHOP HEADER ========== -->
<section class="shop-header reveal">
  <h1>Our Collections</h1>
  <p>Temukan pakaian thrift berkualitas dengan harga terbaik.</p>
</section>


<!-- ========== FILTER BUTTONS ========== -->
<div class="filters reveal">
  <?php
  $filters = ['Semua', 'Pria', 'Wanita', 'Aksesoris', 'Diskon'];
  foreach ($filters as $i => $f): ?>
    <button type="button" class="<?= $i === 0 ? 'active' : '' ?>" data-filter="<?= strtolower($f) ?>">
      <?= htmlspecialchars($f) ?>
    </button>
  <?php endforeach; ?>
</div>


<!-- ========== PRODUCT GRID ========== -->
<section class="container mt-4 mb-5 reveal">
  <div class="text-center mb-4">
    <h3 class="section-title"><strong>TRENDING</strong></h3>
    <p class="subtitle">Koleksi terpilih untuk kamu</p>
  </div>

  <div class="row g-4">
    <?php
    $products = [
      ['foto/baju1.jpg', 'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju2.jpg', 'Kaos Vintage 90s', 'Rp 80.000', 5],
      ['foto/baju3.jpg', 'Celana Cargo Coklat', 'Rp 100.000', 4],
      ['foto/baju4.jpg', 'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju5.jpg', 'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju6.jpg', 'Kaos Vintage 90s', 'Rp 80.000', 5],
      ['foto/baju7.jpg', 'Celana Cargo Coklat', 'Rp 100.000', 4],
      ['foto/baju8.jpg', 'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju9.jpg', 'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju10.jpg', 'Kaos Vintage 90s', 'Rp 80.000', 5],
      ['foto/baju11.jpg', 'Celana Cargo Coklat', 'Rp 100.000', 4],
      ['foto/baju12.jpg', 'Kemeja Flanel Classic', 'Rp 90.000', 5],
    ];

    foreach ($products as [$src, $name, $price, $stars]):
      $rating = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
      ?>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="product-card">
          <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
          <h5><?= htmlspecialchars($name) ?></h5>
          <p><?= htmlspecialchars($price) ?></p>
          <div class="rating"><?= $rating ?></div>
          <button type="button">Tambah ke Keranjang</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>