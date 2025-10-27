<?php
include_once("../config/database.php");

// ===== Ambil daftar user untuk dropdown =====
$user_result = $conn->query("SELECT user_id, nama FROM user ORDER BY user_id ASC");

// ===== Hapus Cart =====
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM cart WHERE cart_id=$id");
  echo "<script>alert('Cart berhasil dihapus!'); window.location='?page=cart';</script>";
  exit;
}

// ===== Tambah Cart =====
if (isset($_POST['simpan'])) {
  $user_id = intval($_POST['user_id']);
  $status = $conn->real_escape_string($_POST['status']);

  $sql = "INSERT INTO cart (user_id, status) VALUES ('$user_id', '$status')";
  if ($conn->query($sql)) {
    echo "<script>alert('Cart berhasil ditambahkan!'); window.location='?page=cart';</script>";
  } else {
    echo "<script>alert('Gagal menambahkan cart: " . addslashes($conn->error) . "');</script>";
  }
}

// ===== Ambil Data Cart =====
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
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center text-white mb-4">Kelola Cart (Keranjang)</h2>

  <!-- FORM TAMBAH CART -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Cart Baru</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-6">
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

          <div class="col-md-6">
            <label>Status</label>
            <select name="status" class="form-select" required>
              <option value="aktif">Aktif</option>
              <option value="checkout">Checkout</option>
              <option value="selesai">Selesai</option>
            </select>
          </div>
        </div>

        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Cart</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TABEL CART -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Data Cart</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>ID Cart</th>
            <th>Nama User</th>
            <th>Tanggal Dibuat</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              // Badge warna sesuai status
              $badgeClass = match ($row['status']) {
                'aktif' => 'bg-success',
                'checkout' => 'bg-warning text-dark',
                'selesai' => 'bg-info text-dark',
                default => 'bg-secondary',
              };
              
              echo "<tr>
                      <td>{$row['cart_id']}</td>
                      <td>{$row['nama']}</td>
                      <td>{$row['tanggal_dibuat']}</td>
                      <td><span class='badge {$badgeClass}'>{$row['status']}</span></td>
                      <td>
                        <a href='?page=cart&hapus={$row['cart_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus cart ini?\")'>Hapus</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='5' class='text-center'>Belum ada data cart</td></tr>";
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
