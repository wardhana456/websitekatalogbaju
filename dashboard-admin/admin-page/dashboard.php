<?php
include_once("../config/database.php");

// ==================== HAPUS USER ====================
if (isset($_GET['hapus'])) {
    $user_id = (int) $_GET['hapus'];
    
    // Menggunakan prepared statement agar aman dari SQL Injection
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User berhasil dihapus!'); window.location='?page=dashboard';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user karena data masih terikat dengan transaksi/keranjang belanja!'); window.location='?page=dashboard';</script>";
    }
    $stmt->close();
}

// ==================== AMBIL DATA USER ====================
$result = $conn->query("SELECT * FROM user ORDER BY user_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Manajemen User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        /* Desain Boxy Minimalis Tanpa Rounded Corners */
        .card-thrift {
            border: 2px solid #000;
            border-radius: 0;
            background-color: #fff;
        }
        .card-thrift-header {
            background-color: #000;
            color: #fff;
            border-radius: 0;
            padding: 15px 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        /* Styling Tabel Minimalis */
        .table-thrift {
            border-collapse: collapse;
        }
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
        /* Tombol Aksi */
        .btn-delete-thrift {
            background-color: #fff;
            color: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 0;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 12px;
            transition: all 0.2s ease;
        }
        .btn-delete-thrift:hover {
            background-color: #dc3545;
            color: #fff;
        }
        .badge-count {
            background-color: #fff;
            color: #000;
            font-weight: 800;
            border-radius: 0;
            padding: 5px 10px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-2 border-dark">
        <h2 class="dashboard-title m-0">Customer Management</h2>
        <span class="text-muted small font-monospace">Role: Admin</span>
    </div>

    <div class="card card-thrift shadow-sm">
        <div class="card-thrift-header d-flex justify-content-between align-items-center">
            <span>Daftar Akun Terdaftar</span>
            <span class="badge badge-count">TOTAL: <?= $result ? $result->num_rows : 0 ?> USER</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-thrift table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-center">
                            <th width="8%">ID User</th>
                            <th width="22%" class="text-start">Nama Lengkap</th>
                            <th width="22%" class="text-start">Email</th>
                            <th width="28%" class="text-start">Alamat Pengiriman</th>
                            <th width="12%">No. HP</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center fw-bold font-monospace">#<?= $row['user_id'] ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="text-secondary"><?= htmlspecialchars($row['email']) ?></td>
                                    <td class="text-muted text-wrap"><?= htmlspecialchars($row['alamat'] ?? '-') ?></td>
                                    <td class="text-center font-monospace"><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <a href="?hapus=<?= $row['user_id'] ?>" 
                                           class="btn btn-delete-thrift d-inline-flex align-items-center gap-1" 
                                           onclick="return confirm('Yakin ingin menghapus permanen akun <?= htmlspecialchars($row['nama']) ?>?')">
                                            <i class="bi bi-trash3"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5 font-monospace">
                                    <i class="bi bi-people mb-2 d-block fs-3"></i> Belum ada customer yang terdaftar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>