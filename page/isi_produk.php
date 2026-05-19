<?php
include_once("../../config/database.php");

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
");
?>

<!-- ========== SHOP HEADER ========== -->
<section class="shop-header reveal">
  <h1>Our Collections</h1>
  <p>Temukan pakaian thrift berkualitas dengan harga terbaik.</p>
</section>


<!-- ========== FILTER BUTTONS ========== -->
<div class="filters reveal">

  <!-- tombol semua -->
  <button type="button" class="filter-btn active" data-filter="semua">

    Semua

  </button>

  <!-- kategori dari database -->
  <?php while ($k = mysqli_fetch_assoc($queryKategori)): ?>

    <button type="button" class="filter-btn" data-filter="<?= strtolower($k['nama_kategori']) ?>">

      <?= htmlspecialchars($k['nama_kategori']) ?>

    </button>

  <?php endwhile; ?>

</div>


<!-- ========== PRODUCT GRID ========== -->
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

      <div class="col-12 col-sm-6 col-lg-3 product-item" data-category="<?= strtolower($p['nama_kategori']) ?>">

        <div class="product-card">

          <!-- gambar -->
          <img src="<?= htmlspecialchars($p['gambar_url']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>"
            loading="lazy">

          <!-- nama produk -->
          <h5>
            <?= htmlspecialchars($p['nama_produk']) ?>
          </h5>

          <!-- harga -->
          <p>
            Rp <?= number_format($p['harga'], 0, ',', '.') ?>
          </p>

          <!-- kategori -->
          <small>
            <?= htmlspecialchars($p['nama_kategori']) ?>
          </small>

          <br><br>

          <!-- tombol -->
          <button type="button">
            Tambah ke Keranjang
          </button>

        </div>

      </div>

    <?php endwhile; ?>

  </div>

</section>

<!-- ========== FILTER SCRIPT ========== -->
<script>

  const filterButtons =
    document.querySelectorAll('.filter-btn');

  const products =
    document.querySelectorAll('.product-item');

  filterButtons.forEach(button => {

    button.addEventListener('click', () => {

      // hapus active
      filterButtons.forEach(btn => {
        btn.classList.remove('active');
      });

      // tambah active
      button.classList.add('active');

      const filter =
        button.dataset.filter;

      products.forEach(product => {

        const category =
          product.dataset.category;

        if (
          filter === 'semua' ||
          filter === category
        ) {

          product.style.display = 'block';

        } else {

          product.style.display = 'none';

        }

      });

    });

  });

</script>