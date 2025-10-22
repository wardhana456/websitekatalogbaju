<body>
  <!-- ========== NAVBAR ========== -->
  <nav class="navbar navbar-expand-lg fixed-top navbar-gradient">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand" href="#">
        <img src="foto/ThriftPay (1).png" alt="Logo" width="160">
      </a>

      <!-- Tombol Hamburger -->
      <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi bi-list" style="font-size: 1.8rem;"></i>
      </button>

      <!-- Isi Navbar -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Menu utama -->
        <ul class="navbar-nav mx-auto mb-3 mb-lg-0 text-center">
          <li class="nav-item"><a class="nav-link fw-bold" href="index.php">Beranda</a></li>
          <li class="nav-item"><a class="nav-link active fw-bold" href="shop.php">Shop</a></li>
          <li class="nav-item"><a class="nav-link fw-bold" href="produk.php">Produk</a></li>
        </ul>

        <!-- Bagian kanan -->
        <div class="topbar d-flex flex-column flex-lg-row align-items-center justify-content-center gap-3 text-center">
          <div class="icon"><i class="bi bi-person-circle"></i></div>
          <div class="search-box">
            <input type="text" placeholder="Search..." />
            <i class="bi bi-search"></i>
          </div>
          <div class="icon"><i class="bi bi-bag"></i></div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Bootstrap JS (wajib untuk hamburger menu) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>