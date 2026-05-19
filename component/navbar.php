<!-- component/navbar.php -->


<nav class="navbar navbar-expand-lg fixed-top navbar-gradient">
  <div class="container-fluid px-3">

    <!-- Logo -->
    <a class="navbar-brand" href="index.php">
      <img src="foto/ThriftPay (1).png" alt="ThriftPay Logo" width="160">
    </a>

    <!-- Hamburger -->
    <button
      class="navbar-toggler text-white border-0"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <i class="bi bi-list" style="font-size: 1.8rem;"></i>
    </button>

    <!-- Menu Navbar -->
    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- Menu Tengah -->
      <ul class="navbar-nav mx-auto mb-3 mb-lg-0 text-center">
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

      <!-- Bagian Kanan -->
      <div class="topbar d-flex flex-column flex-lg-row align-items-center justify-content-center gap-3 text-center">

        <!-- Icon Profile -->
        <div class="icon">
          <i class="bi bi-person-circle"></i>
        </div>

        <!-- Search -->
        <div class="search-box position-relative">
          <input
            type="text"
            class="form-control"
            placeholder="Cari produk..."
            aria-label="Cari produk"
          >

          <i class="bi bi-search search-icon"></i>
        </div>

        <!-- Cart -->
        <div class="icon">
          <i class="bi bi-bag"></i>
        </div>

      </div>

    </div>
  </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>