<?php
include_once("../config/database.php");

// ===== Ambil daftar user untuk dropdown =====
$user_result = $conn->query("SELECT user_id, nama FROM user ORDER BY user_id ASC");

// ===== Hapus Order =====
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM orders WHERE order_id=$id");
  echo "<script>alert('Order berhasil dihapus!'); window.location='?page=order';</script>";
}

// ===== Tambah Order =====
if (isset($_POST['simpan'])) {
  $user_id = intval($_POST['user_id']);
  $total_harga = $conn->real_escape_string($_POST['total_harga']);
  $status = $conn->real_escape_string($_POST['status']);

  $sql = "INSERT INTO orders (user_id, total_harga, status) 
          VALUES ('$user_id', '$total_harga', '$status')";
  if ($conn->query($sql)) {
    echo "<script>alert('Order berhasil ditambahkan!'); window.location='?page=order';</script>";
  } else {
    echo "<script>alert('Gagal menambahkan order: " . addslashes($conn->error) . "');</script>";
  }
}

// ===== Ambil Data Order =====
$result = $conn->query("SELECT o.*, u.nama FROM orders o JOIN user u ON o.user_id = u.user_id ORDER BY o.order_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Order - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center text-white mb-4">Kelola Order</h2>

  <!-- FORM TAMBAH ORDER -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Order Baru</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-4">
            <label>User</label>
            <select name="user_id" class="form-select" required>
              <option value="">Pilih User</option>
              <?php
              if ($user_result->num_rows > 0) {
                while ($user = $user_result->fetch_assoc()) {
                  echo "<option value='{$user['user_id']}'>{$user['nama']} (ID: {$user['user_id']})</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-md-4">
            <label>Total Harga (Rp)</label>
            <input type="number" step="0.01" name="total_harga" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label>Status</label>
            <select name="status" class="form-select" required>
              <option value="menunggu">Menunggu</option>
              <option value="diproses">Diproses</option>
              <option value="dikirim">Dikirim</option>
              <option value="selesai">Selesai</option>
              <option value="dibatalkan">Dibatalkan</option>
            </select>
          </div>
        </div>

        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Order</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TABEL ORDER -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Data Order</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>ID Order</th>
            <th>Nama User</th>
            <th>Tanggal Order</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['order_id']}</td>
                      <td>{$row['nama']}</td>
                      <td>{$row['tanggal_order']}</td>
                      <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                      <td><span class='badge bg-info text-dark'>{$row['status']}</span></td>
                      <td>
                        <a href='?page=order&hapus={$row['order_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus order ini?\")'>Hapus</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='6' class='text-center'>Belum ada order</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
