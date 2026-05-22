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
            nama_produk='$nama', deskripsi='$desk', harga='$harga', 
            harga_diskon='$diskon', stok='$stok', gambar_url='$gambar', kategori_id='$kategori'
          WHERE produk_id='$id'";

  if ($conn->query($sql)) {
    echo "<script>alert('✅ Produk berhasil diperbarui!'); window.location='?page=produk';</script>";
  } else {
    echo "<script>alert('❌ Gagal memperbarui produk: " . addslashes($conn->error) . "');</script>";
  }
}

// ==================== TAMPILKAN DATA PRODUK ====================
$sql_produk = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.kategori_id = k.kategori_id ORDER BY p.produk_id DESC";
$result_produk = $conn->query($sql_produk);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Produk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    /* Sinkronisasi Tema Terang Utama Triftypay */
    :root {
      --border-color: #000000;
      --text-dark: #000000;
      --text-muted: #6c757d;
      --bg-light-gray: #fafafa;
    }

    .dashboard-title {
      font-size: 1.8rem;
      font-weight: 800;
      letter-spacing: -0.5px;
      text-transform: uppercase;
      color: var(--text-dark);
    }

    /* Card Boxy Minimalis */
    .card-thrift {
      border: 2px solid var(--border-color);
      border-radius: 0;
      background-color: #ffffff;
    }
    .card-thrift-header {
      background-color: var(--border-color);
      color: #ffffff;
      border-radius: 0;
      padding: 15px 20px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-size: 0.9rem;
    }

    /* Form Input Boxy */
    .form-thrift input, .form-thrift textarea, .form-thrift select {
      background-color: #ffffff;
      border: 1px solid #cccccc;
      color: var(--text-dark) !important;
      border-radius: 0;
      padding: 10px 15px;
    }
    .form-thrift input:focus, .form-thrift textarea:focus, .form-thrift select:focus {
      background-color: #ffffff;
      border-color: var(--border-color);
      outline: none;
      box-shadow: none;
    }
    .form-thrift label {
      color: var(--text-dark);
      font-size: 0.8rem;
      font-weight: 800;
      text-transform: uppercase;
      margin-bottom: 5px;
      letter-spacing: 0.5px;
    }

    /* Table Minimalis Style */
    .table-thrift {
      border-collapse: collapse;
      margin-bottom: 0;
    }
    .table-thrift thead th {
      background-color: var(--bg-light-gray);
      color: var(--text-dark);
      font-weight: 800;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
      padding: 14px;
      border-bottom: 2px solid var(--border-color);
    }
    .table-thrift tbody td {
      padding: 15px 14px;
      border-bottom: 1px solid #e5e5e5;
      background-color: transparent;
      font-size: 0.9rem;
    }
    .table-thrift tbody tr:hover td {
      background-color: rgba(0, 0, 0, 0.02);
    }

    /* Badges Boxy */
    .badge-thrift {
      border-radius: 0;
      padding: 5px 10px;
      font-weight: 700;
      font-size: 0.75rem;
      text-transform: uppercase;
    }

    /* Tombol Aksi Minimalis */
    .btn-save-thrift {
      background-color: var(--border-color);
      color: #ffffff;
      font-weight: 700;
      text-transform: uppercase;
      border-radius: 0;
      padding: 10px 20px;
      border: none;
      transition: all 0.2s ease;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }
    .btn-save-thrift:hover {
      background-color: #333333;
      color: #ffffff;
    }

    .btn-action-outline {
      border: 1px solid var(--border-color);
      color: var(--text-dark);
      border-radius: 0;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 6px 12px;
      transition: all 0.2s ease;
      background-color: #ffffff;
    }
    .btn-action-outline:hover {
      color: #ffffff;
      background-color: var(--border-color);
    }

    .btn-action-danger {
      border: 1px solid #dc3545;
      color: #dc3545;
      border-radius: 0;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 6px 12px;
      transition: all 0.2s ease;
      background-color: #ffffff;
    }
    .btn-action-danger:hover {
      color: #ffffff;
      background-color: #dc3545;
    }

    /* Image Preview & Thumbnail Boxy */
    .preview-img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border: 2px solid var(--border-color);
      border-radius: 0;
    }
    .img-thrift-thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border: 1px solid var(--border-color);
      border-radius: 0;
    }

    /* Modal Custom (Overriding Bootstrap) */
    .modal.show {
      display: block;
      background: rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(4px);
    }
    .modal-thrift-content {
      background-color: #ffffff;
      border: 2px solid var(--border-color);
      border-radius: 0;
      color: var(--text-dark);
    }
    .modal-thrift-header {
      background-color: var(--border-color);
      color: #ffffff;
      border-radius: 0;
      padding: 15px 20px;
    }
    .badge-count {
      background-color: #000000;
      color: #ffffff;
      font-weight: 800;
      border-radius: 0;
      padding: 6px 12px;
      font-size: 0.75rem;
    }
  </style>
</head>

<body>

<div class="container-fluid p-0">

  <!-- Title Section -->
  <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-2 border-dark">
    <h2 class="dashboard-title m-0">Manajemen Produk</h2>
    <span class="badge badge-count font-monospace">
      <i class="bi bi-box-seam-fill me-1"></i> TOTAL: <?= $result_produk->num_rows ?> PRODUK
    </span>
  </div>

  <!-- ==================== TAMBAH PRODUK ==================== -->
  <div class="card card-thrift shadow-sm mb-4">
    <div class="card-thrift-header">
      <i class="bi bi-plus-circle-fill me-2"></i> Tambah Produk Baru
    </div>
    <div class="card-body p-4">
      <form method="POST" class="form-thrift">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" placeholder="Masukkan nama barang thrift..." required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($kategori_options as $kat): ?>
                <option value="<?= $kat['kategori_id'] ?>">
                  <?= htmlspecialchars($kat['nama_kategori']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tulis kondisi barang, ukuran/size, dan detail minus jika ada..."></textarea>
          </div>

          <div class="col-md-3">
            <label class="form-label">Harga (Rupiah)</label>
            <input type="number" name="harga" class="form-control" placeholder="Contoh: 150000" required>
          </div>

          <div class="col-md-2">
            <label class="form-label">Harga Diskon</label>
            <input type="number" name="harga_diskon" class="form-control" placeholder="Kosongkan jika tidak ada">
          </div>

          <div class="col-md-2">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" value="1" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Pilih Gambar</label>
            <select name="gambar_url" id="selectGambarTambah" class="form-select" onchange="previewImage(this, 'previewTambah')">
              <option value="">-- Pilih File Gambar --</option>
              <?php
              $files = glob("../foto/*.{jpg,jpeg,png,webp}", GLOB_BRACE);
              if ($files) {
                foreach ($files as $file) {
                  $nama_file = basename($file);
                  $path_db = "foto/" . $nama_file;
                  echo "<option value='$path_db'>$nama_file</option>";
                }
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
          <button type="submit" name="simpan" class="btn btn-save-thrift">
            <i class="bi bi-floppy-fill me-1"></i> Simpan Produk
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ==================== TABEL PRODUK ==================== -->
  <div class="card card-thrift shadow-sm mb-5">
    <div class="card-thrift-header">
      <i class="bi bi-collection-fill me-2"></i> Database List Produk
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-thrift table-hover align-middle">
          <thead>
            <tr>
              <th width="8%" class="text-center">ID</th>
              <th width="25%">Produk</th>
              <th width="15%">Kategori</th>
              <th width="15%">Harga Base</th>
              <th width="10%" class="text-center">Stok</th>
              <th width="12%" class="text-center">Gambar</th>
              <th width="15%" class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result_produk->num_rows > 0): ?>
              <?php while ($row = $result_produk->fetch_assoc()): ?>
                <tr>
                  <td class="text-center font-monospace fw-bold text-secondary">#<?= $row['produk_id'] ?></td>
                  <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_produk']) ?></td>
                  <td class="text-secondary"><?= htmlspecialchars($row['nama_kategori'] ?: '-') ?></td>
                  <td class="font-monospace fw-bold text-dark">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                  <td class="text-center">
                    <?php if ($row['stok'] < 5): ?>
                      <span class="badge badge-thrift bg-danger text-white">Sisa <?= $row['stok'] ?></span>
                    <?php else: ?>
                      <span class="badge badge-thrift bg-dark text-white"><?= $row['stok'] ?> Pcs</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <?php if (!empty($row['gambar_url'])): ?>
                      <img src="../<?= htmlspecialchars($row['gambar_url']) ?>" class="img-thrift-thumb">
                    <?php else: ?>
                      <span class="text-muted small font-monospace">No Image</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <div class="d-inline-flex gap-2">
                      <a href="?page=produk&edit=<?= $row['produk_id'] ?>" class="btn btn-action-outline">
                        <i class="bi bi-pencil-square"></i> Edit
                      </a>
                      <a href="?page=produk&hapus=<?= $row['produk_id'] ?>" class="btn btn-action-danger" onclick="return confirm('Yakin hapus produk ini?')">
                        <i class="bi bi-trash3"></i> Hapus
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-5 font-monospace">
                  <i class="bi bi-box-seam d-block fs-3 mb-2"></i> Belum ada produk terdaftar di database.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ==================== EDIT PRODUK (MODAL POPUP POPPING) ==================== -->
<?php
if (isset($_GET['edit'])):
  $produk_id_edit = (int)$_GET['edit'];
  $dataEdit = $conn->query("SELECT * FROM produk WHERE produk_id='$produk_id_edit'")->fetch_assoc();
  if ($dataEdit):
?>
<div class="modal show" style="z-index: 1050;">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content modal-thrift-content shadow-lg">
      <form method="POST" class="form-thrift">
        <div class="modal-header modal-thrift-header">
          <h5 class="modal-title fw-bold text-uppercase" style="font-size:0.9rem; letter-spacing:0.5px;">
            <i class="bi bi-pencil-square me-2"></i> Edit Data Produk
          </h5>
          <a href="?page=produk" class="btn-close btn-close-white" style="box-shadow: none;"></a>
        </div>

        <div class="modal-body p-4">
          <input type="hidden" name="produk_id" value="<?= $dataEdit['produk_id'] ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Produk</label>
              <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($dataEdit['nama_produk']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Kategori</label>
              <select name="kategori_id" class="form-select" required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($kategori_options as $kat): ?>
                  <option value="<?= $kat['kategori_id'] ?>" <?= $kat['kategori_id'] == $dataEdit['kategori_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($dataEdit['deskripsi']) ?></textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label">Harga Base</label>
              <input type="number" name="harga" class="form-control" value="<?= $dataEdit['harga'] ?>" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Harga Diskon</label>
              <input type="number" name="harga_diskon" class="form-control" value="<?= $dataEdit['harga_diskon'] ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label">Stok</label>
              <input type="number" name="stok" class="form-control" value="<?= $dataEdit['stok'] ?>" required>
            </div>

            <div class="col-md-8">
              <label class="form-label">Ubah Gambar Berkas</label>
              <select name="gambar_url" class="form-select" onchange="previewImage(this, 'previewEdit')">
                <option value="">-- Pilih Gambar --</option>
                <?php
                if ($files) {
                  foreach ($files as $file) {
                    $nama_file = basename($file);
                    $path_db = "foto/" . $nama_file;
                    $selected = ($path_db == $dataEdit['gambar_url']) ? 'selected' : '';
                    echo "<option value='$path_db' $selected>$nama_file</option>";
                  }
                }
                ?>
              </select>
            </div>

            <div class="col-md-4 text-center">
              <label class="form-label d-block">Pratinjau Saat Ini</label>
              <img id="previewEdit" src="../<?= htmlspecialchars($dataEdit['gambar_url']) ?>" class="preview-img" onerror="this.src='https://placehold.co/120x120?text=No+Image'">
            </div>
          </div>
        </div>

        <div class="modal-footer border-0 pt-0 px-4 pb-4">
          <a href="?page=produk" class="btn btn-action-outline px-4 py-2 me-2">Batal</a>
          <button type="submit" name="update" class="btn btn-save-thrift px-4 py-2">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ==================== PREVIEW GAMBAR INTERAKTIF ====================
function previewImage(selectElement, previewId){
    const previewImg = document.getElementById(previewId);
    let imagePath = selectElement.value;

    if(imagePath !== ""){
        previewImg.src = "../" + imagePath;
    } else {
        previewImg.src = "https://placehold.co/120x120?text=No+Image";
    }
}
</script>

</body>
</html>
<?php $conn->close(); ?>