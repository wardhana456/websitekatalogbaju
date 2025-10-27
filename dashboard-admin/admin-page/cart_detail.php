<?php
include_once("../config/database.php");

// ===== Ambil daftar Cart untuk dropdown =====
$cart_result = $conn->query("SELECT cart_id, user_id FROM cart ORDER BY cart_id ASC");

// ===== Ambil daftar produk untuk dropdown =====
$produk_result = $conn->query("SELECT produk_id, nama_produk, harga FROM produk ORDER BY produk_id ASC");

// ===== Hapus Cart Detail =====
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM cart_detail WHERE cart_detail_id=$id");
  echo "<script>alert('Cart Detail berhasil dihapus!'); window.location='?page=cart_detail';</script>";
  exit;
}

// ===== Tambah Cart Detail =====
if (isset($_POST['simpan'])) {
  $cart_id = intval($_POST['cart_id']);
  $produk_id = intval($_POST['produk_id']);
  $kuantitas = intval($_POST['kuantitas']);
  if ($kuantitas < 1) $kuantitas = 1;

  // Ambil harga produk dari DB (agar aman)
  $harga = 0.0;
  $getHarga = $conn->prepare("SELECT harga FROM produk WHERE produk_id=?");
  $getHarga->bind_param("i", $produk_id);
  $getHarga->execute();
  $resHarga = $getHarga->get_result();
  if ($resHarga->num_rows > 0) {
    $rowH = $resHarga->fetch_assoc();
    $harga = floatval($rowH['harga']);
  }
  $getHarga->close();

  $subtotal = $harga * $kuantitas;

  $stmt = $conn->prepare("INSERT INTO cart_detail (cart_id, produk_id, kuantitas, subtotal) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("iiid", $cart_id, $produk_id, $kuantitas, $subtotal);
  if ($stmt->execute()) {
    echo "<script>alert('Cart Detail berhasil ditambahkan!'); window.location='?page=cart_detail';</script>";
    $stmt->close();
    $conn->close();
    exit;
  } else {
    echo "<script>alert('Gagal menambahkan cart detail: " . addslashes($stmt->error) . "');</script>";
    $stmt->close();
  }
}

// ===== Ambil Data Cart Detail =====
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
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center text-white mb-4">Kelola Cart Detail</h2>

  <!-- FORM TAMBAH CART DETAIL -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Cart Detail Baru</div>
    <div class="card-body">
      <form method="POST" id="form-cart-detail">
        <div class="row g-3">

          <!-- Cart -->
          <div class="col-md-3">
            <label>Cart</label>
            <select name="cart_id" class="form-select" required>
              <option value="">Pilih Cart</option>
              <?php
              if ($cart_result->num_rows > 0) {
                while ($cart = $cart_result->fetch_assoc()) {
                  echo "<option value='{$cart['cart_id']}'>Cart ID: {$cart['cart_id']}</option>";
                }
              }
              ?>
            </select>
          </div>

          <!-- Produk -->
          <div class="col-md-3">
            <label>Produk</label>
            <select name="produk_id" id="produk" class="form-select" required>
              <option value="">Pilih Produk</option>
              <?php
              if ($produk_result->num_rows > 0) {
                while ($produk = $produk_result->fetch_assoc()) {
                  echo "<option value='{$produk['produk_id']}' data-harga='{$produk['harga']}'>
                          {$produk['nama_produk']} - Rp " . number_format($produk['harga'], 0, ',', '.') . "
                        </option>";
                }
              }
              ?>
            </select>
          </div>

          <!-- Kuantitas -->
          <div class="col-md-2">
            <label>Kuantitas</label>
            <input type="number" name="kuantitas" id="kuantitas" value="1" min="1" class="form-control" required>
          </div>

          <!-- Subtotal -->
          <div class="col-md-4">
            <label>Subtotal (Rp)</label>
            <input type="text" id="subtotal_display" class="form-control mb-2" disabled>
            <input type="hidden" name="subtotal" id="subtotal">
          </div>
        </div>

        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Cart Detail</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TABEL CART DETAIL -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Data Cart Detail</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>ID Detail</th>
            <th>Cart ID</th>
            <th>Produk</th>
            <th>Kuantitas</th>
            <th>Subtotal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['cart_detail_id']}</td>
                      <td>{$row['cart_id']}</td>
                      <td>{$row['nama_produk']}</td>
                      <td>{$row['kuantitas']}</td>
                      <td>Rp " . number_format($row['subtotal'], 0, ',', '.') . "</td>
                      <td>
                        <a href='?page=cart_detail&hapus={$row['cart_detail_id']}' 
                           class='btn btn-danger btn-sm' 
                           onclick='return confirm(\"Yakin hapus detail ini?\")'>
                           Hapus
                        </a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='6' class='text-center'>Belum ada data cart detail</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // Fungsi JS: otomatis hitung subtotal
  function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID').format(num);
  }

  function updateSubtotal() {
    const produk = document.getElementById('produk');
    const qty = parseInt(document.getElementById('kuantitas').value) || 1;
    const selected = produk.options[produk.selectedIndex];
    const harga = selected ? parseFloat(selected.getAttribute('data-harga') || 0) : 0;
    const subtotal = harga * qty;

    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('subtotal_display').value = 'Rp ' + formatRupiah(subtotal);
  }

  document.getElementById('produk').addEventListener('change', updateSubtotal);
  document.getElementById('kuantitas').addEventListener('input', updateSubtotal);

  updateSubtotal(); // inisialisasi awal
</script>

</body>
</html>

<?php $conn->close(); ?>
