<?php
include_once("../config/database.php");

// ========================================
// HAPUS ORDER
// ========================================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Menggunakan prepared statement agar aman dari SQL Injection
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Order berhasil dihapus!');
                window.location='?page=order';
              </script>";
    } else {
        echo "<script>
                alert('Gagal hapus order karena data terikat riwayat!');
                window.location='?page=order';
              </script>";
    }
    $stmt->close();
}

// ========================================
// UPDATE STATUS ORDER (FITUR BARU)
// ========================================
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Status order berhasil diperbarui!');
                window.location='?page=order';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui status order.');
              </script>";
    }
    $stmt->close();
}

// ========================================
// AMBIL DATA ORDER
// ========================================
$result = $conn->query("
    SELECT 
        o.order_id,
        o.tanggal_order,
        o.total_harga,
        o.status,
        u.nama
    FROM orders o
    JOIN user u ON o.user_id = u.user_id
    ORDER BY o.order_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Order - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #fcfcfc;
            color: #000;
            font-family: Arial, sans-serif;
        }
        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        /* Boxy Design (Tanpa Rounded Corners) */
        .card-thrift {
            border: 2px solid #000;
            border-radius: 0;
            background-color: #fff;
        }
        .card-thrift-header {
            background-color: #000;
            color: #fff;
            border-radius: 0;
            padding: 14px 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }
        /* Table Customization */
        .table-thrift thead {
            border-bottom: 2px solid #000;
        }
        .table-thrift th {
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 12px;
            background-color: #fafafa;
        }
        .table-thrift td {
            font-size: 0.9rem;
            padding: 14px 12px;
            border-bottom: 1px solid #e5e5e5;
        }
        /* Status Badges Minimalis */
        .badge-status {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 5px 10px;
            border-radius: 0;
        }
        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-dibayar { background-color: #d4edda; color: #155724; }
        .status-dikirim { background-color: #cce5ff; color: #004085; }
        .status-selesai { background-color: #000; color: #fff; }
        .status-dibatalkan { background-color: #f8d7da; color: #721c24; }

        /* Buttons Style */
        .btn-thrift-sm {
            border-radius: 0;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 10px;
        }
        .btn-thrift-outline-dark { background-color: #fff; color: #000; border: 1px solid #000; }
        .btn-thrift-outline-dark:hover { background-color: #000; color: #fff; }
        .btn-thrift-outline-danger { background-color: #fff; color: #dc3545; border: 1px solid #dc3545; }
        .btn-thrift-outline-danger:hover { background-color: #dc3545; color: #fff; }

        /* Modal Customization */
        .modal-thrift-content {
            border: 3px solid #000;
            border-radius: 0;
        }
        .modal-thrift-header {
            background-color: #fafafa;
            border-bottom: 2px solid #000;
            border-radius: 0;
        }
        .form-thrift select {
            border: 1px solid #000;
            border-radius: 0;
            padding: 10px;
        }
        .form-thrift select:focus {
            box-shadow: none;
            border-color: #000;
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-2 border-dark">
        <h2 class="dashboard-title m-0">Order Management</h2>
        <span class="text-muted small font-monospace">Role: Admin</span>
    </div>

    <div class="card card-thrift shadow-sm">
        <div class="card-thrift-header d-flex justify-content-between align-items-center">
            <span>Daftar Transaksi Masuk</span>
            <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="border-radius:0;">TOTAL: <?= $result->num_rows ?> ORDER</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-thrift table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center">
                            <th width="8%">ID Order</th>
                            <th width="22%" class="text-start">Nama Pelanggan</th>
                            <th width="20%">Tanggal Transaksi</th>
                            <th width="18%" class="text-end">Total Tagihan</th>
                            <th width="14%">Status</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center fw-bold font-monospace">#<?= $row['order_id']; ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td class="text-center text-muted"><?= date('d M Y, H:i', strtotime($row['tanggal_order'])); ?></td>
                                    <td class="text-end fw-bold text-dark font-monospace">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-status status-<?= $row['status']; ?>">
                                            <?= $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="?page=order&edit_status=<?= $row['order_id']; ?>" class="btn btn-thrift-sm btn-thrift-outline-dark">
                                                <i class="bi bi-pencil-square"></i> Status
                                            </a>
                                            <a href="?page=order&hapus=<?= $row['order_id']; ?>" 
                                               class="btn btn-thrift-sm btn-thrift-outline-danger"
                                               onclick="return confirm('Yakin ingin menghapus permanen riwayat order ini?')">
                                                <i class="bi bi-trash3"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5 font-monospace">
                                    <i class="bi bi-receipt-cutoff d-block fs-3 mb-2"></i> Belum ada data pesanan masuk.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($_GET['edit_status'])) {
    $id_edit = intval($_GET['edit_status']);
    $data_order = $conn->query("SELECT order_id, status FROM orders WHERE order_id = $id_edit")->fetch_assoc();
    
    if ($data_order) {
?>
<div class="modal fade show" style="display:block; background-color:rgba(0,0,0,0.6); backdrop-filter: blur(2px);">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content modal-thrift-content shadow-lg">
            <form method="POST" class="form-thrift">
                <input type="hidden" name="order_id" value="<?= $data_order['order_id'] ?>">
                
                <div class="modal-header modal-thrift-header">
                    <h5 class="modal-title fw-bold text-uppercase" style="font-size:0.95rem; letter-spacing:0.5px;">
                        <i class="bi bi-pencil-square me-1"></i> Update Status #<?= $data_order['order_id'] ?>
                    </h5>
                </div>
                
                <div class="modal-body p-4">
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-uppercase font-monospace text-muted">Pilih Status Baru:</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?= $data_order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="dibayar" <?= $data_order['status'] == 'dibayar' ? 'selected' : '' ?>>Dibayar</option>
                            <option value="dikirim" <?= $data_order['status'] == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="selesai" <?= $data_order['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="dibatalkan" <?= $data_order['status'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0">
                    <a href="?page=order" class="btn btn-thrift-sm btn-thrift-outline-dark px-3 py-2">Batal</a>
                    <button type="submit" name="update_status" class="btn btn-thrift-sm btn-thrift-outline-dark bg-black text-white px-3 py-2">
                        Simpan Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    }
} 
?>

</body>
</html>

<?php
$conn->close();
?>