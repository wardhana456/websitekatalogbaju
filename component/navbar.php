<?php
// component/navbar.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data user dari session
$namaUser = $_SESSION['nama'] ?? 'Guest';
$isLogin  = isset($_SESSION['user_id']);

// Huruf pertama avatar
$firstLetter = strtoupper(substr($namaUser, 0, 1));
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>

/* =========================A
   MENU
========================= */
.nav-link{
  color:rgba(255,255,255,.78)!important;
  margin:0 .3rem;
  position:relative;
  transition:.2s;
}

.nav-link:hover{
  color:#fff!important;
}

.nav-link::after{
  content:'';
  position:absolute;
  left:50%;
  bottom:-4px;
  width:0;
  height:2px;
  background:#e94560;
  transition:.25s;
  transform:translateX(-50%);
}

.nav-link:hover::after{
  width:70%;
}

/* =========================
   ICON
========================= */
.icon a,
.icon i{
  color:#fff;
  font-size:1.3rem;
  transition:.2s;
  cursor:pointer;
}

.icon:hover i{
  color:#e94560;
  transform:translateY(-2px);
}

/* =========================
   USER AVATAR
========================= */
.user-avatar{
  width:42px;
  height:42px;
  border-radius:50%;
  background: linear-gradient(135deg, #a67b5b, #6f4e37);
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-weight:700;
  font-size:1rem;
  text-transform:uppercase;
  transition:.2s;
}

.user-avatar:hover{
  transform:scale(1.05);
}

.user-name{
  color:#fff;
  font-size:.9rem;
}



/* =========================
   RESPONSIVE
========================= */
@media(max-width:991px){

  .navbar-collapse{
    margin-top:1rem;
    padding:1rem;
    border-radius:16px;
    background:rgba(255,255,255,.05);
    backdrop-filter: blur(12px);
  }

  .topbar{
    margin-top:1rem;
  }

  .search-box{
    width:100%;
  }

  .user-name{
    display:none;
  }
}
</style>

<nav class="navbar navbar-expand-lg fixed-top navbar-gradient">
  <div class="container-fluid px-3">

    <!-- LOGO -->
    <a class="navbar-brand" href="index.php">
      <img src="foto/ThriftPay.png" alt="ThriftPay Logo" width="160">
    </a>

    <!-- HAMBURGER -->
    <button
      class="navbar-toggler text-white border-0 shadow-none"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <i class="bi bi-list" style="font-size:1.8rem;"></i>
    </button>

    <!-- MENU -->
    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- MENU TENGAH -->
      <ul class="navbar-nav mx-auto mb-3 mb-lg-0 text-center">

        <li class="nav-item">
          <a class="nav-link fw-semibold" href="beranda.php">
            Beranda
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link fw-semibold" href="produk.php">
            Produk
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link fw-semibold" href="shop.php">
            Shop
          </a>
        </li>

      </ul>

      <!-- RIGHT SIDE -->
      <div class="topbar d-flex flex-column flex-lg-row align-items-center gap-3">

        <!-- SEARCH -->
        <div class="search-box position-relative">
          <input
            type="text"
            class="form-control"
            placeholder="Cari produk..."
            aria-label="Cari produk"
          >

          <i class="bi bi-search search-icon"></i>
        </div>


        <!-- USER -->
        <div class="dropdown">

          <button
            class="btn border-0 shadow-none d-flex align-items-center gap-2 p-0 dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >

            <!-- AVATAR -->
            <div class="user-avatar">
              <?= htmlspecialchars($firstLetter) ?>
            </div>

            <!-- NAMA -->
            <span class="user-name fw-semibold">
              <?= htmlspecialchars($namaUser) ?>
            </span>

          </button>

          <!-- DROPDOWN -->
          <ul class="dropdown-menu dropdown-menu-end">

            <?php if ($isLogin): ?>

              <li>
                <a class="dropdown-item" href="profile.php">
                  <i class="bi bi-person me-2"></i>
                  Profile
                </a>
              </li>

              <li>
                <a class="dropdown-item" href="keranjang.php">
                  <i class="bi bi-box me-2"></i>
                  Keranjang Saya
                </a>
              </li>

              <li>
              <a class="dropdown-item" href="pesanan.php">
              <i class="bi bi-box-seam me-2"></i>
               Pesanan Saya
              </a>
              </li>

              <li><hr class="dropdown-divider"></li>

              <li>
                <a
                  class="dropdown-item text-danger"
                  href="logout.php"
                  onclick="return confirm('Yakin ingin logout?')"
                >
                  <i class="bi bi-box-arrow-right me-2"></i>
                  Logout
                </a>
              </li>

            <?php else: ?>

              <li>
                <a class="dropdown-item" href="login.php">
                  <i class="bi bi-box-arrow-in-right me-2"></i>
                  Login
                </a>
              </li>

              <li>
                <a class="dropdown-item" href="sign_in.php">
                  <i class="bi bi-person-plus me-2"></i>
                  Daftar
                </a>
              </li>

            <?php endif; ?>

          </ul>

        </div>

      </div>

    </div>
  </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>