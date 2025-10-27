<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Triftypay Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #111418;
      --bg-sidebar: #1a1f25;
      --accent: #00d4ff;
      --text-light: #e6edf3;
      --text-muted: #9ba3af;
      --radius: 14px;
    }

    body {
      background-color: var(--bg-dark);
      color: var(--text-light);
      font-family: "Public Sans", sans-serif;
      margin: 0;
      overflow-x: hidden;
    }

    /* === SIDEBAR === */
    .sidebar {
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      background: var(--bg-sidebar);
      box-shadow: 2px 0 10px rgba(0,0,0,0.4);
      padding: 1rem 0;
      display: flex;
      flex-direction: column;
    }

    .sidebar h2 {
      text-align: center;
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 1.5rem;
    }

    .sidebar a {
      color: var(--text-muted);
      text-decoration: none;
      padding: 0.8rem 1.5rem;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      border-radius: var(--radius);
      margin: 0 1rem;
    }

    .sidebar a i {
      font-size: 1.3rem;
      margin-right: 0.8rem;
    }

    .sidebar a.active, .sidebar a:hover {
      background: var(--accent);
      color: #000;
      font-weight: 600;
    }

    /* === HEADER === */
    header {
      position: fixed;
      left: 250px;
      top: 0;
      right: 0;
      height: 65px;
      background: #20252b;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
      z-index: 10;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    header .search-bar {
      background: rgba(255,255,255,0.1);
      border-radius: var(--radius);
      padding: 0.5rem 1rem;
      display: flex;
      align-items: center;
      width: 300px;
    }

    header input {
      background: transparent;
      border: none;
      outline: none;
      color: var(--text-light);
      margin-left: 0.5rem;
      width: 100%;
    }

    header .user img {
      border-radius: 50%;
      border: 2px solid var(--accent);
    }

    /* === MAIN CONTENT === */
    main {
      margin-left: 250px;
      padding: 100px 30px 30px; /* Tambah jarak dari header */
      min-height: 100vh;
      background-color: #161a1e;
    }

    .card {
      background-color: #1f242a;
      border: none;
      border-radius: var(--radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      color: var(--text-light);
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    .card h5 {
      color: var(--accent);
      font-weight: 600;
    }

    /* === FOOTER === */
    footer {
      margin-left: 250px;
      background: #1a1f25;
      color: var(--text-muted);
      text-align: center;
      padding: 1rem 0;
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    /* Responsive */
    @media (max-width: 991px) {
      .sidebar {
        position: fixed;
        left: -250px;
        transition: all 0.4s ease;
      }

      .sidebar.active {
        left: 0;
      }

      header {
        left: 0;
      }

      main, footer {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <nav class="sidebar">
    <h2>Triftypay</h2>
      <a href="?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="?page=kategori"><i class="bi bi-tags"></i> Kategori</a>
      <a href="?page=produk"><i class="bi bi-box-seam"></i> Produk</a>
      <a href="?page=order"><i class="bi bi-bag-check"></i> Order</a>
      <a href="?page=order_detail"><i class="bi bi-receipt"></i> Order Detail</a>
      <a href="?page=cart"><i class="bi bi-cart4"></i> Cart</a>
      <a href="?page=cart_detail"><i class="bi bi-basket"></i> Cart Detail</a>
      <a href="?page=review"><i class="bi bi-chat-left-text"></i> Review</a>
  </nav>
  <!-- Header -->
  <header>
    <div class="search-bar">
      <i class="bi bi-search"></i>
      <input type="text" placeholder="Cari sesuatu...">
    </div>
    <div class="user d-flex align-items-center">
      <img src="./assets/images/user/avatar-2.jpg" alt="avatar" width="35" height="35" class="me-2">
      <span>Admin</span>
    </div>
  </header>

  <!-- Main Content -->
  <main>
    <?php
      $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
      switch ($page) {
          case 'dashboard':
              include "./admin-page/dashboard.php";
              break;
          case 'produk':
              include "./admin-page/produk.php";
              break;
           case 'kategori':
               include "./admin-page/kategori.php";
               break;
           case 'order':
               include "./admin-page/order.php";
                break;
           case 'order_detail':
               include "./admin-page/order_detail.php";
               break;
            case 'cart':
                include "./admin-page/cart.php";
                break;
            case 'cart_detail':
                include "./admin-page/cart_detail.php";
                break;
            case 'review':
                include "./admin-page/review.php";
                break;
          default:
              include "./admin-page/dashboard.php";
              break;
      }
    ?>
  </main>

  <!-- Footer -->
  <footer>
    <p>© 2025 Triftypay | Tema Dark Modern</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
