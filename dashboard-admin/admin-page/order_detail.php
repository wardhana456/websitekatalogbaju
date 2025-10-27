<?php
include_once("../config/database.php");

// ===== Ambil daftar order untuk dropdown =====
$order_result = $conn->query("SELECT order_id FROM orders ORDER BY order_id ASC");

// ===== Ambil daftar produk untuk dropdown =====
$produk_result = $conn->query("SELECT produk_id, nama_produk, harga FROM produk ORDER BY produk_id ASC");

// ===== Hapus Order Detail =====
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM order_detail WHERE order_detail_id=$id");
  echo "<script>alert('Order Detail berhasil dihapus!'); window.location='?page=order_detail';</script>";
}

// ===== Tambah Order Detail =====
if (isset($_POST['simpan'])) {
  $order_id = intval($_POST['order_id']);
  $produk_id = intval($_POST['produk_id']);
  $kuantitas = intval($_POST['kuantitas']);
  $subtotal = floatval($_POST['subtotal']);

  $sql = "INSERT INTO order_detail (order_id, produk_id, kuantitas, subtotal)
          VALUES ('$order_id', '$produk_id', '$kuantitas', '$subtotal')";
  if ($conn->query($sql)) {
    echo "<script>alert('Order Detail berhasil ditambahkan!'); window.location='?page=order_detail';</script>";
  } else {
    echo "<script>alert('Gagal menambahkan order detail: " . addslashes($conn->error) . "');</script>";
  }
}

// ===== Ambil Data Order Detail =====
$result = $conn->query("
  SELECT od.*, o.order_id, p.nama_produk, p.harga
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
  <title>Kelola Order Detail - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center text-white mb-4">Kelola Order Detail</h2>

  <!-- FORM TAMBAH ORDER DETAIL -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Order Detail Baru</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">

          <!-- Order -->
          <div class="col-md-3">
            <label>Order</label>
            <select name="order_id" class="form-select" required>
              <option value="">Pilih Order</option>
              <?php
              if ($order_result->num_rows > 0) {
                while ($order = $order_result->fetch_assoc()) {
                  echo "<option value='{$order['order_id']}'>Order ID: {$order['order_id']}</option>";
                }
              }
              ?>
            </select>
          </div>

          <!-- Produk -->
          <div class="col-md-3">
            <label>Produk</label>
            <select name="produk_id" class="form-select" required>
              <option value="">Pilih Produk</option>
              <?php
              if ($produk_result->num_rows > 0) {
                while ($produk = $produk_result->fetch_assoc()) {
                  echo "<option value='{$produk['produk_id']}'>{$produk['nama_produk']} - Rp " . number_format($produk['harga'], 0, ',', '.') . "</option>";
                }
              }
              ?>
            </select>
          </div>

          <!-- Kuantitas -->
          <div class="col-md-3">
            <label>Kuantitas</label>
            <input type="number" name="kuantitas" value="1" min="1" class="form-control" required>
          </div>

          <!-- Subtotal -->
          <div class="col-md-3">
            <label>Subtotal (Rp)</label>
            <input type="number" step="0.01" name="subtotal" class="form-control" required>
          </div>
        </div>

        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Detail</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TABEL ORDER DETAIL -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Data Order Detail</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>ID Detail</th>
            <th>Order ID</th>
            <th>Produk</th>
            <th>Kuantitas</th>
            <th>Subtotal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['order_detail_id']}</td>
                      <td>{$row['order_id']}</td>
                      <td>{$row['nama_produk']}</td>
                      <td>{$row['kuantitas']}</td>
                      <td>Rp " . number_format($row['subtotal'], 0, ',', '.') . "</td>
                      <td>
                        <a href='?page=order_detail&hapus={$row['order_detail_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus detail ini?\")'>Hapus</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='6' class='text-center'>Belum ada data order detail</td></tr>";
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
