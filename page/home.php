<?php /* ===== page/home.php — Konten halaman Beranda ===== */ ?>

<!-- ========== KATEGORI ========== -->
<section class="container my-5 reveal">
  <div class="section-title-home mb-1 ms-1">Kategori</div>
  <p class="subtitle ms-1 mb-3">Temukan koleksi favoritmu</p>

  <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3 category-row">
    <?php
    /* Loop kategori — ganti dengan data dinamis dari DB jika ada */
    $categories = [
      ['foto/baju1.jpg', 'Jaket'],
      ['foto/baju2.jpg', 'Kaos'],
      ['foto/baju3.jpg', 'Celana'],
      ['foto/baju4.jpg', 'Kemeja'],
      ['foto/baju5.jpg', 'Aksesoris'],
    ];
    foreach ($categories as [$src, $label]) : ?>
      <div class="col">
        <img src="<?= htmlspecialchars($src) ?>" class="product-img" alt="<?= htmlspecialchars($label) ?>">
      </div>
    <?php endforeach; ?>
  </div>
</section>


<!-- ========== TODAY SALE ========== -->
<section class="container mb-5 reveal">
  <div class="section-title-home mb-1">Today Sale!! 🔥</div>
  <p class="subtitle mb-3">Promo terbatas — jangan sampai kehabisan!</p>

  <div class="sale-collage">
    <div class="sale-item large">
      <img src="foto/promosi1.jpg" alt="Promo Utama" loading="lazy">
    </div>
    <div class="sale-item small top">
      <img src="foto/promosi1.jpg" alt="Promo 2" loading="lazy">
    </div>
    <div class="sale-item small bottom">
      <img src="foto/promosi1.jpg" alt="Promo 3" loading="lazy">
    </div>
  </div>
</section>


<!-- ========== TRENDING PRODUCTS ========== -->
<section class="container my-5 reveal">
  <div class="text-center mb-4">
    <h3 class="section-title"><strong>TRENDING</strong></h3>
    <p class="subtitle">Pilihan terpopuler minggu ini</p>
  </div>

  <div class="row g-4">
    <?php
    /* Data produk — idealnya diambil dari database */
    $products = [
      ['foto/baju1.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju2.jpg',  'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju3.jpg',  'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju4.jpg',  'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju5.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju6.jpg',  'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju7.jpg',  'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju8.jpg',  'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju9.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju10.jpg', 'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju11.jpg', 'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju12.jpg', 'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju9.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju10.jpg', 'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju11.jpg', 'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju12.jpg', 'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju1.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju2.jpg',  'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju3.jpg',  'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju4.jpg',  'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju5.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju6.jpg',  'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju7.jpg',  'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju8.jpg',  'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju9.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju10.jpg', 'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju11.jpg', 'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju12.jpg', 'Kemeja Flanel Classic', 'Rp 90.000', 5],
      ['foto/baju9.jpg',  'Jaket Denim Oversize', 'Rp 120.000', 4],
      ['foto/baju10.jpg', 'Kaos Vintage 90s',     'Rp 80.000',  5],
      ['foto/baju11.jpg', 'Celana Cargo Coklat',  'Rp 100.000', 4],
      ['foto/baju12.jpg', 'Kemeja Flanel Classic', 'Rp 90.000', 5],
    ];

    foreach ($products as [$src, $name, $price, $stars]) :
      /* Render bintang rating */
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

  <!-- Load More -->
  <div class="text-center mt-5">
    <button class="btn-load" type="button">Load More</button>
  </div>
</section>