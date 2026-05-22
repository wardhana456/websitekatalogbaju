<?php
include_once("../config/database.php");

// ==================== HAPUS CART ====================
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM cart WHERE cart_id=$id");
  echo "<script>alert('🗑️ Cart berhasil dihapus!'); window.location='?page=cart';</script>";
  exit;
}

// ==================== AMBIL DATA CART ====================
$result = $conn->query("
  SELECT c.*, u.nama 
  FROM cart c 
  JOIN user u ON c.user_id = u.user_id 
  ORDER BY c.cart_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Cart - Admin Dashboard</title>
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

    /* Badges Status Boxy & High Contrast */
    .badge-thrift {
      border-radius: 0;
      padding: 6px 12px;
      font-weight: 800;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 1px solid var(--border-color);
    }
    .badge-aktif {
      background-color: #28a745;
      color: #ffffff;
    }
    .badge-checkout {
      background-color: #ffc107;
      color: #000000;
    }
    .badge-selesai {
      background-color: #0d6efd;
      color: #ffffff;
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

    .badge-count {
      background-color: #000000;
      color: #ffffff;
      font-weight: 800;
      border-radius: 0;
      padding: 6px 12px;
      font-size: 0.75rem;
    }
  </style>
</head>
<body>

<div class="container-fluid p-0">

  <!-- Title Section -->
  <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-2 border-dark">
    <h2 class="dashboard-title m-0">Aktivitas Keranjang</h2>
    <span class="badge badge-count font-monospace">
      <i class="bi bi-cart-fill me-1"></i> TOTAL: <?= $result->num_rows ?> CART
    </span>
  </div>

  <!-- ==================== TABEL DATA CART ==================== -->
  <div class="card card-thrift shadow-sm">
    <div class="card-thrift-header">
      <i class="bi bi-list-stars me-2"></i> Log Keranjang Belanja User
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-thrift table-hover align-middle">
          <thead>
            <tr>
              <th width="10%" class="text-center">ID Cart</th>
              <th width="30%">Nama Pembeli / User</th>
              <th width="25%">Tanggal Dibuat</th>
              <th width="20%" class="text-center">Status Sesi</th>
              <th width="15%" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                  // Penentuan kelas badge kustom berdasarkan status
                  $statusClass = match ($row['status']) {
                    'aktif' => 'badge-aktif',
                    'checkout' => 'badge-checkout',
                    'selesai' => 'badge-selesai',
                    default => 'bg-secondary text-white',
                  };
                ?>
                <tr>
                  <td class="text-center font-monospace fw-bold text-secondary">#<?= $row['cart_id'] ?></td>
                  <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama']) ?></td>
                  <td class="text-secondary font-monospace"><?= $row['tanggal_dibuat'] ?></td>
                  <td class="text-center">
                    <span class="badge badge-thrift <?= $statusClass ?>">
                      <?= htmlspecialchars($row['status']) ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <a href="?page=cart&hapus=<?= $row['cart_id'] ?>" class="btn btn-action-danger" onclick="return confirm('Yakin ingin menghapus data keranjang ini?')">
                      <i class="bi bi-trash3"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-5 font-monospace">
                  <i class="bi bi-cart-x d-block fs-3 mb-2"></i> Belum ada aktivitas keranjang belanja saat ini.
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