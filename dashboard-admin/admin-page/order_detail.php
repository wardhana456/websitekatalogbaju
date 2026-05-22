<?php
include_once("../config/database.php");

// ========================================
// AMBIL DATA ORDER DETAIL
// ========================================
$result = $conn->query("
    SELECT 
        od.order_detail_id, 
        od.order_id, 
        p.nama_produk, 
        od.kuantitas, 
        od.subtotal
    FROM order_detail od
    JOIN orders o ON od.order_id = o.order_id
    JOIN produk p ON od.produk_id = p.produk_id
    ORDER BY od.order_detail_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Order Detail - Admin Dashboard</title>
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
    </style>
</head>
<body>

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-2 border-dark">
        <h2 class="dashboard-title m-0">Order Detail</h2>
        <span class="text-muted small font-monospace">Role: Admin</span>
    </div>

    <div class="card card-thrift shadow-sm">
        <div class="card-thrift-header d-flex justify-content-between align-items-center">
            <span>Daftar Item Rincian Order</span>
            <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="border-radius:0;">TOTAL: <?= $result->num_rows ?> DATA</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-thrift table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center">
                            <th width="12%">ID Detail</th>
                            <th width="12%">ID Order</th>
                            <th width="36%" class="text-start">Nama Produk</th>
                            <th width="15%">Kuantitas</th>
                            <th width="25%" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center fw-bold font-monospace">#<?= $row['order_detail_id']; ?></td>
                                    <td class="text-center font-monospace">#<?= $row['order_id']; ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_produk']); ?></td>
                                    <td class="text-center font-monospace"><?= $row['kuantitas']; ?> Pcs</td>
                                    <td class="text-end fw-bold text-dark font-monospace">Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5 font-monospace">
                                    <i class="bi bi-box-seam d-block fs-3 mb-2"></i> Belum ada data rincian order.
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

<?php
$conn->close();
?>