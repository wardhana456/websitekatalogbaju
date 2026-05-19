<?php

include_once("config/database.php");

/* =========================
   AMBIL DATA KATEGORI
========================= */
$queryKategori = mysqli_query($conn, "
    SELECT * FROM kategori
");

/* =========================
   AMBIL DATA PRODUK
========================= */
$queryProduk = mysqli_query($conn, "
    SELECT produk.*, kategori.nama_kategori
    FROM produk
    LEFT JOIN kategori
    ON produk.kategori_id = kategori.kategori_id
    ORDER BY produk.produk_id DESC
");

/* =========================
   AMBIL PRODUK TRENDING
========================= */
$queryTrending = mysqli_query($conn, "
    SELECT * FROM produk
    ORDER BY produk_id DESC
    LIMIT 12
");

?>

<!-- ========== KATEGORI ========== -->
<section class="container my-5 reveal">

  <div class="section-title-home mb-1 ms-1">
    Kategori
  </div>

  <p class="subtitle ms-1 mb-3">
    Temukan koleksi favoritmu
  </p>

  <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 g-3 category-row">

    <?php while($k = mysqli_fetch_assoc($queryKategori)): ?>

      <div class="col">

        <div class="category-card text-center">

          <h5>
            <?= htmlspecialchars($k['nama_kategori']) ?>
          </h5>

        </div>

      </div>

    <?php endwhile; ?>

  </div>

</section>

<!-- ========== TODAY SALE ========== -->
<section class="container mb-5 reveal">

  <div class="section-title-home mb-1">
    Today Sale!! 🔥
  </div>

  <p class="subtitle mb-3">
    Promo terbatas — jangan sampai kehabisan!
  </p>

  <div class="sale-collage">

    <div class="sale-item large">
      <img src="foto/promosi1.jpg" alt="Promo Utama">
    </div>

    <div class="sale-item small top">
      <img src="foto/promosi1.jpg" alt="Promo 2">
    </div>

    <div class="sale-item small bottom">
      <img src="foto/promosi1.jpg" alt="Promo 3">
    </div>

  </div>

</section>

<!-- ========== TRENDING PRODUCTS ========== -->
<section class="container my-5 reveal">

  <div class="text-center mb-4">

    <h3 class="section-title">
      <strong>TRENDING</strong>
    </h3>

    <p class="subtitle">
      Pilihan terpopuler minggu ini
    </p>

  </div>

  <div class="row g-4">

    <?php while($p = mysqli_fetch_assoc($queryTrending)): ?>

      <div class="col-12 col-sm-6 col-lg-3">

        <div class="product-card">

          <!-- gambar -->
          <img
            src="<?= htmlspecialchars($p['gambar_url']) ?>"
            alt="<?= htmlspecialchars($p['nama_produk']) ?>"
            loading="lazy"
          >

          <!-- nama produk -->
          <h5>
            <?= htmlspecialchars($p['nama_produk']) ?>
          </h5>

          <!-- harga -->
          <p>
            Rp <?= number_format($p['harga'],0,',','.') ?>
          </p>

          <!-- tombol -->
          <button type="button">
            Tambah ke Keranjang
          </button>

        </div>

      </div>

    <?php endwhile; ?>

  </div>

  <!-- Load More -->
  <div class="text-center mt-5">

    <button class="btn-load" type="button">
      Load More
    </button>

  </div>

</section>