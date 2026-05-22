<?php
include_once("../config/database.php");

// ==================== LOGIKA HAPUS USER (DARI KODE ANDA) ====================
if (isset($_GET['hapus'])) {
    $user_id = (int) $_GET['hapus'];
    
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User berhasil dihapus!'); window.location='?page=dashboard';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user karena data masih terikat dengan transaksi/keranjang belanja!'); window.location='?page=dashboard';</script>";
    }
    $stmt->close();
}

// Mengambil parameter halaman saat ini, default-nya adalah 'dashboard'
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

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
      --bg-light: #fcfcfc;
      --bg-sidebar: #ffffff;
      --bg-header: #ffffff;
      --accent-dark: #000000;
      --text-dark: #000000;
      --text-muted: #6c757d;
      --border-color: #000000;
    }

    body {
      background-color: var(--bg-light);
      color: var(--text-dark);
      font-family: Arial, sans-serif;
      margin: 0;
      overflow-x: hidden;
    }

    .logo-container{
        text-align:center;
        margin-bottom:30px;
        padding:10px;
    }

    .logo-img{
        width:180px;
        height: 80px;
    }

    /* === SIDEBAR (TEMA TERANG & BOXY) === */
    .sidebar {
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      background: var(--bg-sidebar);
      border-right: 2px solid var(--border-color);
      padding: 1.5rem 0;
      display: flex;
      flex-direction: column;
    }

    .sidebar h2 {
      text-align: center;
      font-weight: 800;
      color: var(--text-dark);
      margin-bottom: 2rem;
      letter-spacing: -0.5px;
      text-transform: uppercase;
    }

    .sidebar a {
      color: #333333;
      text-decoration: none;
      padding: 0.8rem 1.5rem;
      display: flex;
      align-items: center;
      transition: all 0.2s ease;
      margin: 0.1rem 0;
      font-size: 0.9rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .sidebar a i {
      font-size: 1.2rem;
      margin-right: 0.8rem;
    }

    /* Efek Hover & Aktif Boxy (Hitam Solid) */
    .sidebar a.active, .sidebar a:hover {
      background: var(--accent-dark);
      color: #ffffff;
    }

    /* === HEADER === */
    header {
      position: fixed;
      left: 250px;
      top: 0;
      right: 0;
      height: 65px;
      background: var(--bg-header);
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding: 0 2rem;
      z-index: 10;
      border-bottom: 2px solid var(--border-color);
    }

    header .user img {
      border-radius: 0; /* Mengubah jadi boxy sesuai tema */
      border: 2px solid var(--border-color);
    }

    header .user span {
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }

    /* === MAIN CONTENT === */
    main {
      margin-left: 250px;
      padding: 95px 30px 30px; 
      min-height: calc(100vh - 57px); 
      background-color: var(--bg-light);
    }


    /* Responsive */
    @media (max-width: 991px) {
      .sidebar {
        position: fixed;
        left: -250px;
        transition: all 0.4s ease;
        z-index: 100;
      }

      .sidebar.active {
        left: 0;
      }

      header {
        left: 0;
      }

    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <nav class="sidebar">
    <div class="logo-container">
    <img src="../foto/ThriftPay dashboard.png" alt="Triftypay Logo" class="logo-img">
    </div>
    <a href="?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="?page=kategori" class="<?= $page == 'kategori' ? 'active' : '' ?>"><i class="bi bi-tags"></i> Kategori</a>
    <a href="?page=produk" class="<?= $page == 'produk' ? 'active' : '' ?>"><i class="bi bi-box-seam"></i> Produk</a>
    <a href="?page=order" class="<?= $page == 'order' ? 'active' : '' ?>"><i class="bi bi-bag-check"></i> Order</a>
    <a href="?page=order_detail" class="<?= $page == 'order_detail' ? 'active' : '' ?>"><i class="bi bi-receipt"></i> Order Detail</a>
    <a href="?page=cart" class="<?= $page == 'cart' ? 'active' : '' ?>"><i class="bi bi-cart4"></i> Cart</a>
    <a href="?page=cart_detail" class="<?= $page == 'cart_detail' ? 'active' : '' ?>"><i class="bi bi-basket"></i> Cart Detail</a>
    <a href="?page=review" class="<?= $page == 'review' ? 'active' : '' ?>"><i class="bi bi-chat-left-text"></i> Review</a>
  </nav>

  <!-- Main Content -->
  <main>
    <?php
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


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>