<!-- ========== NAVBAR ========== -->
<nav class="navbar navbar-expand-lg fixed-top navbar-gradient">
  <div class="container-fluid px-3">

    <!-- Logo -->
    <a class="navbar-brand" href="index.php">
      <img src="foto/ThriftPay (1).png" alt="ThriftPay Logo" width="148" height="auto">
    </a>

    <!-- Hamburger -->
    <button
      class="navbar-toggler border-0 text-white"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <i class="bi bi-list fs-2"></i>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- Nav Links (tengah) -->
      <ul class="navbar-nav mx-auto gap-1 text-center">
        <li class="nav-item">
          <a class="nav-link fw-bold" href="index.php">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold" href="produk.php">Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold" href="shop.php">Shop</a>
        </li>
      </ul>

      <!-- Kanan: Icon + Search -->
      <div class="topbar d-flex flex-column flex-lg-row align-items-center gap-3 py-2 py-lg-0">
        <div class="icon" title="Profil"><i class="bi bi-person-circle"></i></div>

        <div class="search-box">
          <input type="text" placeholder="Cari produk..." aria-label="Cari produk">
          <i class="bi bi-search"></i>
        </div>

        <div class="icon" title="Keranjang"><i class="bi bi-bag"></i></div>
      </div>

    </div>
  </div>
</nav>

<!-- ========== JS Aktifkan nav-link active otomatis ========== -->
<script>
  /* Tandai nav-link yang sesuai halaman aktif */
  (function () {
    const current = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
      if (link.getAttribute('href') === current) link.classList.add('active');
    });
  })();
</script>