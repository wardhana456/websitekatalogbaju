<?php
include_once("../config/database.php");

// ==================== HAPUS CART DETAIL ====================
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM cart_detail WHERE cart_detail_id=$id");
  echo "<script>alert('🗑️ Cart Detail berhasil dihapus!'); window.location='?page=cart_detail';</script>";
  exit;
}

// ==================== AMBIL DATA CART DETAIL ====================
$result = $conn->query("
  SELECT cd.*, c.cart_id, p.nama_produk, p.harga
  FROM cart_detail cd
  JOIN cart c ON cd.cart_id = c.cart_id
  JOIN produk p ON cd.produk_id = p.produk_id
  ORDER BY cd.cart_detail_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Cart Detail - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    /* Sinkronisasi Tema Terang Utama Triftypay */
    :root {
      --border-color: #000000;
      --text-dark: #000000;
      --text-muted: #6c757d;
      --bg-light-gray: #fafafa;
    }

    .dashboard-title {
      font-size: 1.8rem;
      font-weight: 800;
      letter-spacing: -0.5px;
      text-transform: uppercase;
      color: var(--text-dark);
    }

    /* Card Boxy Minimalis */
    .card-thrift {
      border: 2px solid var(--border-color);
      border-radius: 0;
      background-color: #ffffff;
    }
    .card-thrift-header {
      background-color: var(--border-color);
      color: #ffffff;
      border-radius: 0;
      padding: 15px 20px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-size: 0.9rem;
    }

    /* Table Minimalis Style */
    .table-thrift {
      border-collapse: collapse;
      margin-bottom: 0;
    }
    .table-thrift thead th {
      background-color: var(--bg-light-gray);
      color: var(--text-dark);
      font-weight: 800;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
      padding: 14px;
      border-bottom: 2px solid var(--border-color);
    }
    .table-thrift tbody td {
      padding: 15px 14px;
      border-bottom: 1px solid #e5e5e5;
      background-color: transparent;
      font-size: 0.9rem;
    }
    .table-thrift tbody tr:hover td {
      background-color: rgba(0, 0, 0, 0.02);
    }

    /* Badges & Counter Boxy */
    .badge-thrift {
      border-radius: 0;
      padding: 5px 10px;
      font-weight: 700;
      font-size: 0.75rem;
    }
    .badge-count {
      background-color: #000000;
      color: #ffffff;
      font-weight: 800;
      border-radius: 0;
      padding: 6px 12px;
      font-size: 0.75rem;
    }

    /* Tombol Hapus Minimalis */
    .btn-action-danger {
      border: 1px solid #dc3545;
      color: #dc3545;
      border-radius: 0;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 6px 12px;
      transition: all 0.2s ease;
      background-color: #ffffff;
    }
    .btn-action-danger:hover {
      color: #ffffff;
      background-color: #dc3545;
    }
  </style>
</head>
<body>

<div class="container-fluid p-0">

  <!-- Title Section -->
  <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-2 border-dark">
    <h2 class="dashboard-title m-0">Detail Item Keranjang</h2>
    <span class="badge badge-count font-monospace">
      <i class="bi bi-tags-fill me-1"></i> TOTAL: <?= $result ? $result->num_rows : 0 ?> ITEM
    </span>
  </div>

  <!-- ==================== TABEL DATA CART DETAIL ==================== -->
  <div class="card card-thrift shadow-sm">
    <div class="card-thrift-header">
      <i class="bi bi-list-check me-2"></i> Rincian Barang di Dalam Keranjang
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-thrift table-hover align-middle">
          <thead>
            <tr>
              <th width="10%" class="text-center">ID Detail</th>
              <th width="12%" class="text-center">Cart ID</th>
              <th width="30%">Nama Produk Thrift</th>
              <th width="13%" class="text-center">Qty</th>
              <th width="20%">Subtotal Belanja</th>
              <th width="15%" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td class="text-center font-monospace fw-bold text-secondary">#<?= $row['cart_detail_id'] ?></td>
                  <td class="text-center font-monospace fw-bold text-dark">ID-<?= $row['cart_id'] ?></td>
                  <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_produk']) ?></td>
                  <td class="text-center">
                    <span class="badge badge-thrift bg-light text-dark border border-dark font-monospace fw-bold">
                      <?= $row['kuantitas'] ?>x
                    </span>
                  </td>
                  <td class="font-monospace fw-bold text-dark">
                    Rp <?= number_format($row['subtotal'], 0, ',', '.') ?>
                  </td>
                  <td class="text-center">
                    <a href="?page=cart_detail&hapus=<?= $row['cart_detail_id'] ?>" class="btn btn-action-danger" onclick="return confirm('Yakin ingin menghapus item produk ini dari keranjang?')">
                      <i class="bi bi-trash3"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-5 font-monospace">
                  <i class="bi bi-basket-x d-block fs-3 mb-2"></i> Tidak ada rincian item produk di dalam database keranjang.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

</body>
</html>
<?php $conn->close(); ?>