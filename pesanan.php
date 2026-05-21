<?php
// pesanan.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("config/database.php");

// Proteksi: User wajib login untuk melihat riwayat pesanan
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu untuk melihat pesanan Anda.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = intval($_SESSION['user_id']);

/* =====================================================================
   AMBIL DATA ORDERS & DETAIL PRODUK (JOIN MULTI-TABLE)
   Kita urutkan dari yang paling baru (DESC) agar user gampang memantau
===================================================================== */
$query = mysqli_prepare($conn, "
    SELECT 
        o.order_id, 
        o.tanggal_order, 
        o.total_harga, 
        o.status,
        od.kuantitas,
        od.subtotal AS total_item,
        p.nama_produk,
        p.harga AS harga_satuan,
        p.gambar_url
    FROM orders o
    JOIN order_detail od ON o.order_id = od.order_id
    JOIN produk p ON od.produk_id = p.produk_id
    WHERE o.user_id = ?
    ORDER BY o.order_id DESC, od.order_detail_id ASC
");
mysqli_stmt_bind_param($query, "i", $user_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

// Mengelompokkan data detail produk berdasarkan ID Order-nya
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $order_id = $row['order_id'];
    
    // Jika order_id belum ada di array kelompok, inisialisasi datanya dulu
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'tanggal_order' => $row['tanggal_order'],
            'total_harga'   => $row['total_harga'],
            'status'        => $row['status'],
            'items'         => []
        ];
    }
    
    // Masukkan detail produk ke dalam kelompok order tersebut
    $orders[$order_id]['items'][] = [
        'nama_produk'  => $row['nama_produk'],
        'harga_satuan' => $row['harga_satuan'],
        'kuantitas'    => $row['kuantitas'],
        'total_item'   => $row['total_item'],
        'gambar_url'   => $row['gambar_url']
    ];
}
?>

<?php include("component/header.php"); ?>

   <style>
    body {
      background-color: #f8f9fa;
      color: #000;
      font-family: Arial, sans-serif;
      padding-top: 100px;
    }
    .page-title {
      font-size: 2.2rem;
      font-weight: 800;
      letter-spacing: -1px;
      text-transform: uppercase;
      margin-bottom: 30px;
    }
    /* Card Grouping Order */
    .order-card {
      background: #fff;
      border: 1px solid #000;
      border-radius: 0; /* Desain boxy / minimalis */
      margin-bottom: 25px;
      padding: 20px;
    }
    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e5e5e5;
      padding-bottom: 12px;
      margin-bottom: 15px;
    }
    .order-id {
      font-weight: 800;
      letter-spacing: 0.5px;
    }
    .order-date {
      font-size: 0.85rem;
      color: #666;
    }
    /* Item produk di dalam order */
    .product-row {
      display: flex;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px dashed #f0f0f0;
    }
    .product-row:last-child {
      border-bottom: none;
    }
    .product-img {
      width: 70px;
      height: 85px;
      object-fit: cover;
      background-color: #f5f5f5;
    }
    .product-info h6 {
      font-size: 1rem;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 4px;
    }
    /* Badge Status Custom */
    .badge-status {
      font-size: 0.75rem;
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.5px;
      padding: 6px 12px;
      border-radius: 0;
    }
    .status-pending { background-color: #ffeeba; color: #856404; }
    .status-dibayar { background-color: #d4edda; color: #155724; }
    .status-dikirim { background-color: #cce5ff; color: #004085; }
    .status-selesai { background-color: #000; color: #fff; }
    .status-dibatalkan { background-color: #f8d7da; color: #721c24; }

    .order-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid #e5e5e5;
      padding-top: 15px;
      margin-top: 15px;
    }
    .total-amount {
      font-weight: 800;
      font-size: 1.1rem;
    }
  </style>
</head>
<body>

<?php include("component/navbar.php"); ?>

<div class="container my-5">
  <h1 class="page-title text-center text-md-start">Pesanan Saya</h1>

  <?php if (empty($orders)): ?>
    <div class="text-center my-5 py-5 bg-white border border-secondary">
      <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
      <h4 class="mt-3 fw-bold">Belum Ada Pesanan</h4>
      <p class="text-muted">Kamu belum melakukan transaksi apa pun di ThriftPay.</p>
      <a href="shop.php" class="btn btn-dark rounded-0 px-4 mt-2">Cari Produk Sekarang</a>
    </div>
  <?php else: ?>

    <?php foreach ($orders as $id_order => $data): ?>
      <div class="order-card shadow-sm">
        
        <div class="order-header flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
          <div>
            <span class="order-id">ORDER #<?= $id_order ?></span>
            <span class="mx-2 text-muted d-none d-sm-inline">|</span>
            <span class="order-date"><?= date('d M Y, H:i', strtotime($data['tanggal_order'])) ?> WIB</span>
          </div>
          
          <?php 
            $status_class = 'status-' . $data['status']; 
            $status_text = $data['status'];
          ?>
          <span class="badge badge-status <?= $status_class ?>">
            <?= htmlspecialchars($status_text) ?>
          </span>
        </div>

        <div class="order-body">
          <?php foreach ($data['items'] as $item): ?>
            <div class="product-row">
              <div class="me-3">
                <img src="<?= htmlspecialchars($item['gambar_url']) ?>" class="product-img" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
              </div>
              <div class="product-info flex-grow-1">
                <h6><?= htmlspecialchars($item['nama_produk']) ?></h6>
                <div class="text-muted small">
                  Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?> x <?= $item['kuantitas'] ?>
                </div>
              </div>
              <div class="text-end fw-semibold">
                Rp <?= number_format($item['total_item'], 0, ',', '.') ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="order-footer">
          <div class="text-muted small d-none d-sm-block">
            Terima kasih telah berbelanja pakaian thrift berkualitas.
          </div>
          <div class="text-end w-100 text-sm-end">
            <span class="text-muted me-2">Total Pesanan:</span>
            <span class="total-amount text-danger">Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></span>
          </div>
        </div>

      </div>
    <?php endforeach; ?>

  <?php endif; ?>
</div>

</body>
</html>