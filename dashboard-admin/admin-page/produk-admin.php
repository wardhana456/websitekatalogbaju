<?php
// ==================== CONFIGURASI DATABASE ====================
$host = "localhost";
$user = "root";
$pass = "";
$db = "websitekatalogbaju";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// ==================== TAMBAH DATA ====================
if (isset($_POST['tambah'])) {
  $nama = $_POST['nama_produk'];
  $desk = $_POST['deskripsi'];
  $harga = $_POST['harga'];
$diskon = isset($_POST['harga_diskon']) && $_POST['harga_diskon'] !== '' ? $_POST['harga_diskon'] : 0;
  $stok = $_POST['stok'];
  $gambar = $_POST['gambar_url'];
  $kategori = $_POST['kategori_id'];
  $conn->query("INSERT INTO produk (nama_produk, deskripsi, harga, harga_diskon, stok, gambar_url, kategori_id)
                VALUES ('$nama', '$desk', '$harga', '$diskon', '$stok', '$gambar', '$kategori')");
 header("Location: ?page=produk");
exit;
}

// ==================== HAPUS DATA ====================
if (isset($_GET['hapus'])) {
  $email = $_GET['hapus'];
  $conn->query("DELETE FROM produk WHERE produk_id='$produk_id'");
  echo "<script>alert('User berhasil dihapus!'); window.location='?page=dashboard';</script>";
}

// ==================== EDIT DATA ====================
if (isset($_POST['update'])) {
  $id = $_POST['produk_id'];
  $nama = $_POST['nama_produk'];
  $desk = $_POST['deskripsi'];
  $harga = $_POST['harga'];
  $diskon = $_POST['harga_diskon'];
  $stok = $_POST['stok'];
  $gambar = $_POST['gambar_url'];
  $kategori = $_POST['kategori_id'];

  $conn->query("UPDATE produk SET 
                nama_produk='$nama',
                deskripsi='$desk',
                harga='$harga',
                harga_diskon='$diskon',
                stok='$stok',
                gambar_url='$gambar',
                kategori_id='$kategori'
                WHERE produk_id=$id");
  header("Location: dashboard_produk.php");
  exit;
}

// ==================== AMBIL DATA UNTUK EDIT ====================
$edit_data = null;
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $edit_data = $conn->query("SELECT * FROM produk WHERE produk_id=$id")->fetch_assoc();
}

// ==================== TAMPIL DATA ====================
$result = $conn->query("SELECT * FROM produk ORDER BY produk_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Produk - Website Katalog Baju</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .container { max-width: 900px; }
    .card { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    img { border-radius: 8px; }
  </style>
</head>
<body>
<div class="container mt-5 mb-5">
  <h2 class="text-center text-white mb-4"> Dashboard Produk - Katalog Baju</h2>

  <!-- Form Tambah / Edit Produk -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">
      <?= $edit_data ? "Edit Produk" : "Tambah Produk Baru" ?>
    </div>
    <div class="card-body">
      <form method="POST">
        <?php if ($edit_data): ?>
          <input type="hidden" name="produk_id" value="<?= $edit_data['produk_id'] ?>">
        <?php endif; ?>
        <div class="mb-2">
          <input type="text" name="nama_produk" class="form-control" placeholder="Nama Produk" required value="<?= $edit_data['nama_produk'] ?? '' ?>">
        </div>
        <div class="mb-2">
          <textarea name="deskripsi" class="form-control" placeholder="Deskripsi"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
        </div>
        <div class="mb-2">
          <input type="number" step="0.01" name="harga" class="form-control" placeholder="Harga" required value="<?= $edit_data['harga'] ?? '' ?>">
        </div>
        <div class="mb-2">
          <input type="number" step="0.01" name="harga_diskon" class="form-control" placeholder="Harga Diskon (Opsional)" value="<?= $edit_data['harga_diskon'] ?? '' ?>">
        </div>
        <div class="mb-2">
          <input type="number" name="stok" class="form-control" placeholder="Stok" value="<?= $edit_data['stok'] ?? '' ?>">
        </div>
        <div class="mb-2">
          <input type="text" name="gambar_url" class="form-control" placeholder="URL Gambar" value="<?= $edit_data['gambar_url'] ?? '' ?>">
        </div>
        <div class="mb-2">
          <input type="number" name="kategori_id" class="form-control" placeholder="ID Kategori" value="<?= $edit_data['kategori_id'] ?? '' ?>">
        </div>
        <button type="submit" name="<?= $edit_data ? 'update' : 'tambah' ?>" class="btn btn-success">
          <?= $edit_data ? ' Update Produk' : '➕ Simpan Produk' ?>
        </button>
        <?php if ($edit_data): ?>
          <a href="dashboard_produk.php" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <!-- Tabel Daftar Produk -->
  <div class="card">
    <div class="card-header bg-dark text-white">
      Daftar Produk
    </div>
    <div class="card-body">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Gambar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['produk_id'] ?></td>
                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td>Rp<?= number_format($row['harga'], 2, ',', '.') ?></td>
                <td><?= $row['stok'] ?></td>
                <td><img src="<?= $row['gambar_url'] ?>" width="60"></td>
                <td class="text-center">
                  <a href="?edit=<?= $row['produk_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="?hapus=<?= $row['produk_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus produk ini?')">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">Belum ada produk</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>
