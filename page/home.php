<?php
// shop.php
include_once(__DIR__ . "/../config/database.php");

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
      
      <div class="col-12 col-sm-6 col-lg-3 product-item" data-category="<?= strtolower($p['nama_kategori']) ?>">
        <div class="product-card">

          <a href="detail_produk.php?id=<?= $p['produk_id'] ?>" class="product-card-link">
            <img src="<?= htmlspecialchars($p['gambar_url']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" loading="lazy">

            <h5><?= htmlspecialchars($p['nama_produk']) ?></h5>
          </a>

          <p class="mb-1">
            Rp <?= number_format($p['harga'], 0, ',', '.') ?>
          </p>

          <small>
            <?= htmlspecialchars($p['nama_kategori'] ?? 'Uncategorized') ?>
          </small>

          <br><br>

          <button type="button">
            Tambah ke Keranjang
          </button>

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