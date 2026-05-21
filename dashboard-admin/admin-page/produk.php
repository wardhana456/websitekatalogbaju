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

DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .modal.show {
      display: block;
      background: rgba(0, 0, 0, 0.6);
    }
    .preview-img {
      max-width: 100%;
      height: 120px;
      object-fit: cover;
      border: 2px dashed #ddd;
      border-radius: 8px;
    }
  </style>
</head>
<body class="bg-dark text-light">

<div class="container mt-5">
  <h2 class="text-center mb-4 text-white"> Dashboard Admin - Manajemen Produk</h2>

  <div class="card mb-4 shadow-sm text-dark">
    <div class="card-header bg-primary text-white fw-bold">Tambah Produk Baru</div>
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
                <option value="<?= $kat['kategori_id'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
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
          <div class="col-md-2">
            <label class="form-label">Harga Diskon</label>
            <input type="number" step="0.01" name="harga_diskon" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Pilih Gambar</label>
            <select name="gambar_url" id="selectGambarTambah" class="form-select" onchange="previewImage(this, 'previewTambah')">
              <option value="">-- Pilih File Gambar --</option>
              <?php
              $files = glob("foto/*.{jpg,jpeg,png,webp}", GLOB_BRACE);
              foreach ($files as $file) {
                  $nama_file = basename($file);
                  echo "<option value='foto/$nama_file'>$nama_file</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-2 text-center">
            <label class="form-label d-block">Pratinjau</label>
            <img id="previewTambah" src="https://placehold.co/120x120?text=No+Image" class="preview-img" alt="Preview">
          </div>
        </div>
        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Produk</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm mb-5 text-dark">
    <div class="card-header bg-light text-dark fw-bold">Daftar Produk</div>
    <div class="card-body p-0">
      <table class="table table-bordered table-striped table-hover m-0">
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
              <tr class="align-middle">
                <td class="text-center"><?= $row['produk_id'] ?></td>
                <td><strong><?= htmlspecialchars($row['nama_produk']) ?></strong></td>
                <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                <td>Rp <?= $harga_tampil ?> <?= $harga_diskon_tampil ?></td>
                <td class="text-center"><?= $badge_stok ?></td>
                <td class="text-center">
                  <?php if (!empty($row['gambar_url'])): ?>
                    <img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="Thumbnail" style="width: 50px; height: 50px; object-fit: cover; class="img-thumbnail"">
                  <?php else: ?>
                    <span class="text-muted">Tidak ada</span>
                  <?php endif; ?>
                </td>
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
  if ($dataEdit):
?>
<div class="modal show animate" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg text-dark" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">✏️ Edit Produk: <?= htmlspecialchars($dataEdit['nama_produk']) ?></h5>
          <a href="?page=produk" class="btn-close"></a>
        </div>
        <div class="modal-body">
          <input type="hidden" name="produk_id" value="<?= $dataEdit['produk_id'] ?>">
          
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Produk</label>
              <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($dataEdit['nama_produk']) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kategori</label>
              <select name="kategori_id" class="form-select" required>
                <?php foreach ($kategori_options as $kat): ?>
                  <option value="<?= $kat['kategori_id'] ?>" <?= $kat['kategori_id'] == $dataEdit['kategori_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($dataEdit['deskripsi']) ?></textarea>
          </div>

          <div class="row g-3 align-items-end">
            <div class="col-md-3">
              <label class="form-label">Harga (Rp)</label>
              <input type="number" step="0.01" name="harga" class="form-control" value="<?= $dataEdit['harga'] ?>" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">Harga Diskon</label>
              <input type="number" step="0.01" name="harga_diskon" class="form-control" value="<?= $dataEdit['harga_diskon'] ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Stok</label>
              <input type="number" name="stok" class="form-control" value="<?= $dataEdit['stok'] ?>" required>
            </div>
<div class="col-md-3">

    <label class="form-label">
        Pilih Gambar Baru
    </label>

    <select 
        name="gambar_url"
        id="selectGambarEdit"
        class="form-select"
        onchange="previewImage(this.value, 'previewEdit')"
    >

        <option value="">
            -- Pilih File Gambar --
        </option>

        <?php

        // ambil semua file gambar dari folder foto
        $files = glob("../../foto/*.{jpg,jpeg,png,webp}", GLOB_BRACE);

        if ($files) {
            foreach ($files as $file) {
                // ambil nama file saja
                $nama_file = basename($file);

                // path yang disimpan ke database (relatif ke folder ini)
                $path_db = "foto/" . $nama_file;

                // selected otomatis
                $selected = (!empty($dataEdit['gambar_url']) && $path_db === $dataEdit['gambar_url']) ? 'selected' : '';

                echo "
                    <option value='" . $path_db . "' " . $selected . ">
                        " . htmlspecialchars($nama_file) . "
                    </option>
                ";
            }
        }
        ?>

    </select>

</div>
              </select>
            </div>
            <div class="col-md-2 text-center">
              <img id="previewEdit" src="<?= !empty($dataEdit['gambar_url']) ? $dataEdit['gambar_url'] : 'https://placehold.co/120x120?text=No+Image' ?>" class="preview-img" alt="Preview">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="?page=produk" class="btn btn-secondary">Batal</a>
          <button type="submit" name="update" class="btn btn-warning fw-bold">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; endif; ?>

<?php $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Fungsi JavaScript untuk mendeteksi perubahan select option dan memperbarui src img pratinjau
function previewImage(selectElement, previewId) {
    const previewImg = document.getElementById(previewId);
    if (selectElement.value) {
        previewImg.src = selectElement.value;
    } else {
        previewImg.src = "https://placehold.co/120x120?text=No+Image";
    }
}

<script>

function previewImage(src, previewId){

    document.getElementById(previewId).src = "../" + src;

}

</script>
</script>
</body>
</html>