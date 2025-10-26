<?php
include_once("../config/database.php");

// ==================== AMBIL KATEGORI UNTUK SELECT ====================
$kategori_options = [];
$result_kat = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
while ($row = $result_kat->fetch_assoc()) {
  $kategori_options[] = $row;
}

// ==================== TAMBAH PRODUK ====================
if (isset($_POST['simpan'])) {
  $nama = $conn->real_escape_string($_POST['nama_produk']);
  $desk = $conn->real_escape_string($_POST['deskripsi']);
  $harga = $_POST['harga'];
  $diskon = isset($_POST['harga_diskon']) && $_POST['harga_diskon'] !== '' ? $_POST['harga_diskon'] : 0;
  $stok = $_POST['stok'];
  $gambar = $conn->real_escape_string($_POST['gambar_url']);
  $kategori = $_POST['kategori_id'];

  $sql = "INSERT INTO produk (nama_produk, deskripsi, harga, harga_diskon, stok, gambar_url, kategori_id)
          VALUES ('$nama', '$desk', '$harga', '$diskon', '$stok', '$gambar', '$kategori')";
  if ($conn->query($sql)) {
    echo "<script>alert('✅ Produk berhasil ditambahkan!'); window.location='?page=produk';</script>";
  } else {
    echo "<script>alert('❌ Gagal menambahkan produk: " . addslashes($conn->error) . "');</script>";
  }
}

// ==================== HAPUS PRODUK ====================
if (isset($_GET['hapus'])) {
  $id = (int) $_GET['hapus'];
  $conn->query("DELETE FROM produk WHERE produk_id='$id'");
  echo "<script>alert('🗑️ Produk berhasil dihapus!'); window.location='?page=produk';</script>";
  exit;
}

// ==================== UPDATE PRODUK ====================
if (isset($_POST['update'])) {
  $id = $_POST['produk_id'];
  $nama = $conn->real_escape_string($_POST['nama_produk']);
  $desk = $conn->real_escape_string($_POST['deskripsi']);
  $harga = $_POST['harga'];
  $diskon = isset($_POST['harga_diskon']) && $_POST['harga_diskon'] !== '' ? $_POST['harga_diskon'] : 0;
  $stok = $_POST['stok'];
  $gambar = $conn->real_escape_string($_POST['gambar_url']);
  $kategori = $_POST['kategori_id'];

  $sql = "UPDATE produk SET 
            nama_produk='$nama',
            deskripsi='$desk',
            harga='$harga',
            harga_diskon='$diskon',
            stok='$stok',
            gambar_url='$gambar',
            kategori_id='$kategori'
          WHERE produk_id='$id'";
  if ($conn->query($sql)) {
    echo "<script>alert('✅ Produk berhasil diperbarui!'); window.location='?page=produk';</script>";
  } else {
    echo "<script>alert('❌ Gagal memperbarui produk: " . addslashes($conn->error) . "');</script>";
  }
}

// ==================== TAMPILKAN DATA PRODUK ====================
$sql_produk = "SELECT p.*, k.nama_kategori 
               FROM produk p 
               LEFT JOIN kategori k ON p.kategori_id = k.kategori_id 
               ORDER BY p.produk_id DESC";
$result_produk = $conn->query($sql_produk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center mb-4 text-white"> Dashboard Admin - Manajemen Produk</h2>

  <!-- FORM TAMBAH PRODUK -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Produk Baru</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($kategori_options as $kat): ?>
                <option value="<?= $kat['kategori_id'] ?>"><?= $kat['nama_kategori'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" step="0.01" name="harga" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Harga Diskon (Opsional)</label>
            <input type="number" step="0.01" name="harga_diskon" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">URL Gambar</label>
            <input type="url" name="gambar_url" class="form-control">
          </div>
        </div>
        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Produk</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TABEL PRODUK -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Daftar Produk</div>
    <div class="card-body">
      <table class="table table-bordered table-hover table-sm">
        <thead class="table-primary text-center">
          <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Gambar</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result_produk->num_rows > 0): ?>
            <?php while ($row = $result_produk->fetch_assoc()): ?>
              <?php
                $harga_tampil = number_format($row['harga'], 0, ',', '.');
                $harga_diskon_tampil = $row['harga_diskon'] > 0
                  ? "<br><span class='badge bg-danger'>Diskon: Rp " . number_format($row['harga_diskon'], 0, ',', '.') . "</span>"
                  : "";
                $badge_stok = $row['stok'] < 10
                  ? "<span class='badge bg-danger'>{$row['stok']} (Kritis)</span>"
                  : "<span class='badge bg-success'>{$row['stok']}</span>";
              ?>
              <tr>
                <td><?= $row['produk_id'] ?></td>
                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                <td>Rp <?= $harga_tampil ?> <?= $harga_diskon_tampil ?></td>
                <td><?= $badge_stok ?></td>
                <td><a href="<?= $row['gambar_url'] ?>" target="_blank">Lihat</a></td>
                <td class="text-center">
                  <a href="?page=produk&edit=<?= $row['produk_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="?page=produk&hapus=<?= $row['produk_id'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Yakin ingin menghapus produk <?= htmlspecialchars($row['nama_produk']) ?>?')">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center">Belum ada produk</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if (isset($_GET['edit'])):
  $produk_id_edit = (int)$_GET['edit'];
  $dataEdit = $conn->query("SELECT * FROM produk WHERE produk_id='$produk_id_edit'")->fetch_assoc();
?>
<div class="modal show" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title">✏️ Edit Produk: <?= $dataEdit['nama_produk'] ?></h5>
          <a href="?page=produk" class="btn-close"></a>
        </div>
        <div class="modal-body">
          <input type="hidden" name="produk_id" value="<?= $dataEdit['produk_id'] ?>">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Produk</label>
              <input type="text" name="nama_produk" class="form-control" value="<?= $dataEdit['nama_produk'] ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kategori</label>
              <select name="kategori_id" class="form-select" required>
                <?php foreach ($kategori_options as $kat): ?>
                  <option value="<?= $kat['kategori_id'] ?>" <?= $kat['kategori_id'] == $dataEdit['kategori_id'] ? 'selected' : '' ?>>
                    <?= $kat['nama_kategori'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= $dataEdit['deskripsi'] ?></textarea>
          </div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Harga</label>
              <input type="number" step="0.01" name="harga" class="form-control" value="<?= $dataEdit['harga'] ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Harga Diskon</label>
              <input type="number" step="0.01" name="harga_diskon" class="form-control" value="<?= $dataEdit['harga_diskon'] ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Stok</label>
              <input type="number" name="stok" class="form-control" value="<?= $dataEdit['stok'] ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">URL Gambar</label>
              <input type="url" name="gambar_url" class="form-control" value="<?= $dataEdit['gambar_url'] ?>">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="?page=produk" class="btn btn-secondary">Batal</a>
          <button type="submit" name="update" class="btn btn-warning">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>
