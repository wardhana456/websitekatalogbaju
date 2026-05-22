<?php
include_once("../config/database.php");

// ==================== HAPUS REVIEW ====================
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM review WHERE review_id=$id");
  echo "<script>alert('🗑️ Review berhasil dihapus!'); window.location='?page=review';</script>";
  exit;
}

// ==================== AMBIL DATA REVIEW ====================
$result = $conn->query("
  SELECT r.*, u.nama AS nama_user, p.nama_produk 
  FROM review r 
  JOIN user u ON r.user_id = u.user_id 
  JOIN produk p ON r.produk_id = p.produk_id 
  ORDER BY r.review_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Review - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    /* Sinkronisasi Tema Terang Utama */
    :root {
      --border-color: #000000;
      --text-dark: #000000;
      --text-muted: #6c757d;
      --bg-light-gray: #fafafa;
      --star-color: #ffc107;
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
    .badge-count {
      background-color: #000000;
      color: #ffffff;
      font-weight: 800;
      border-radius: 0;
      padding: 6px 12px;
      font-size: 0.75rem;
    }
    .star-icon {
      color: var(--star-color);
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
    <h2 class="dashboard-title m-0">Ulasan & Komentar Pelanggan</h2>
    <span class="badge badge-count font-monospace">
      <i class="bi bi-chat-left-text-fill me-1"></i> TOTAL: <?= $result ? $result->num_rows : 0 ?> REVIEW
    </span>
  </div>

  <!-- ==================== TABEL DATA REVIEW ==================== -->
  <div class="card card-thrift shadow-sm">
    <div class="card-thrift-header">
      <i class="bi bi-stars me-2"></i> Daftar Review Produk Pelanggan
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-thrift table-hover align-middle">
          <thead>
            <tr>
              <th width="10%" class="text-center">ID Review</th>
              <th width="15%">Nama User</th>
              <th width="20%">Produk</th>
              <th width="12%" class="text-center">Rating</th>
              <th width="28%">Komentar / Isi Ulasan</th>
              <th width="15%">Tanggal Review</th>
              <th width="10%" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td class="text-center font-monospace fw-bold text-secondary">#<?= $row['review_id'] ?></td>
                  <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_user']) ?></td>
                  <td class="text-dark"><?= htmlspecialchars($row['nama_produk']) ?></td>
                  <td class="text-center">
                    <span class="font-monospace fw-bold text-dark">
                      <?php 
                      $rating = intval($row['rating']);
                      for ($i = 1; $i <= 5; $i++) {
                          if ($i <= $rating) {
                              echo '<i class="bi bi-star-fill star-icon"></i>';
                          } else {
                              echo '<i class="bi bi-star text-muted"></i>';
                          }
                      }
                      ?>
                    </span>
                  </td>
                  <td class="text-secondary" style="white-space: pre-line; line-height: 1.4;"><?= htmlspecialchars($row['komentar']) ?></td>
                  <td class="font-monospace text-muted" style="font-size: 0.8rem;">
                    <?= date('d/m/Y H:i', strtotime($row['tanggal_review'])) ?>
                  </td>
                  <td class="text-center">
                    <a href="?page=review&hapus=<?= $row['review_id'] ?>" class="btn btn-action-danger" onclick="return confirm('Yakin ingin menghapus review dari user ini?')">
                      <i class="bi bi-trash3"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-5 font-monospace">
                  <i class="bi bi-chat-square-x d-block fs-3 mb-2"></i> Belum ada ulasan produk yang masuk dari pelanggan.
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