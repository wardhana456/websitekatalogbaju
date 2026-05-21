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

  $diskon = isset($_POST['harga_diskon']) && $_POST['harga_diskon'] !== ''
    ? $_POST['harga_diskon']
    : 0;

  $stok = $_POST['stok'];

  $gambar = $conn->real_escape_string($_POST['gambar_url']);

  $kategori = $_POST['kategori_id'];

  $sql = "INSERT INTO produk 
  (
    nama_produk,
    deskripsi,
    harga,
    harga_diskon,
    stok,
    gambar_url,
    kategori_id
  )
  VALUES
  (
    '$nama',
    '$desk',
    '$harga',
    '$diskon',
    '$stok',
    '$gambar',
    '$kategori'
  )";

  if ($conn->query($sql)) {

    echo "
    <script>
      alert('✅ Produk berhasil ditambahkan!');
      window.location='?page=produk';
    </script>
    ";

  } else {

    echo "
    <script>
      alert('❌ Gagal menambahkan produk: " . addslashes($conn->error) . "');
    </script>
    ";
  }
}

// ==================== HAPUS PRODUK ====================
if (isset($_GET['hapus'])) {

  $id = (int) $_GET['hapus'];

  $conn->query("DELETE FROM produk WHERE produk_id='$id'");

  echo "
  <script>
    alert('🗑️ Produk berhasil dihapus!');
    window.location='?page=produk';
  </script>
  ";

  exit;
}

// ==================== UPDATE PRODUK ====================
if (isset($_POST['update'])) {

  $id = $_POST['produk_id'];

  $nama = $conn->real_escape_string($_POST['nama_produk']);
  $desk = $conn->real_escape_string($_POST['deskripsi']);
  $harga = $_POST['harga'];

  $diskon = isset($_POST['harga_diskon']) && $_POST['harga_diskon'] !== ''
    ? $_POST['harga_diskon']
    : 0;

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

    WHERE produk_id='$id'
  ";

  if ($conn->query($sql)) {

    echo "
    <script>
      alert('✅ Produk berhasil diperbarui!');
      window.location='?page=produk';
    </script>
    ";

  } else {

    echo "
    <script>
      alert('❌ Gagal memperbarui produk: " . addslashes($conn->error) . "');
    </script>
    ";
  }
}

// ==================== TAMPILKAN DATA PRODUK ====================
$sql_produk = "
SELECT 
  p.*, 
  k.nama_kategori
FROM produk p
LEFT JOIN kategori k
ON p.kategori_id = k.kategori_id
ORDER BY p.produk_id DESC
";

$result_produk = $conn->query($sql_produk);

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<title>Dashboard Admin - Produk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

html{
  scroll-behavior:smooth;
}

body{
  overflow-x:hidden;
}

.modal.show{
  display:block;
  background:rgba(0,0,0,0.6);
}

.preview-img{
  width:120px;
  height:120px;
  object-fit:cover;
  border:2px dashed #ddd;
  border-radius:10px;
}

</style>

</head>

<body class="bg-dark text-light">

<div class="container mt-5">

<h2 class="text-center mb-4">
  Dashboard Admin - Manajemen Produk
</h2>

<!-- ==================== TAMBAH PRODUK ==================== -->

<div class="card mb-4 shadow-sm text-dark">

<div class="card-header bg-primary text-white fw-bold">
  Tambah Produk Baru
</div>

<div class="card-body">

<form method="POST">

<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Nama Produk</label>

<input
type="text"
name="nama_produk"
class="form-control"
required
>

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

<textarea
name="deskripsi"
class="form-control"
rows="3"
></textarea>

</div>

<div class="col-md-3">

<label class="form-label">Harga</label>

<input
type="number"
name="harga"
class="form-control"
required
>

</div>

<div class="col-md-2">

<label class="form-label">Harga Diskon</label>

<input
type="number"
name="harga_diskon"
class="form-control"
>

</div>

<div class="col-md-2">

<label class="form-label">Stok</label>

<input
type="number"
name="stok"
class="form-control"
required
>

</div>

<!-- ==================== SELECT FOTO ==================== -->

<div class="col-md-3">

<label class="form-label">Pilih Gambar</label>

<select
name="gambar_url"
id="selectGambarTambah"
class="form-select"
onchange="previewImage(this, 'previewTambah')"
>

<option value="">
-- Pilih File Gambar --
</option>

<?php

$files = glob("../foto/*.{jpg,jpeg,png,webp}", GLOB_BRACE);

if ($files) {

  foreach ($files as $file) {

    $nama_file = basename($file);

    $path_db = "foto/" . $nama_file;

    echo "
    <option value='$path_db'>
      $nama_file
    </option>
    ";
  }
}

?>

</select>

</div>

<!-- ==================== PREVIEW ==================== -->

<div class="col-md-2 text-center">

<label class="form-label d-block">
Pratinjau
</label>

<img
id="previewTambah"
src="https://placehold.co/120x120?text=No+Image"
class="preview-img"
alt="Preview"
>

</div>

</div>

<div class="mt-3 text-end">

<button
type="submit"
name="simpan"
class="btn btn-success"
>
💾 Simpan Produk
</button>

</div>

</form>

</div>
</div>

<!-- ==================== TABEL PRODUK ==================== -->

<div class="card shadow-sm mb-5 text-dark">

<div class="card-header bg-light fw-bold">
Daftar Produk
</div>

<div class="card-body p-0">

<table class="table table-bordered table-hover m-0">

<thead class="table-primary text-center">

<tr>
<th>ID</th>
<th>Produk</th>
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

<tr class="align-middle">

<td class="text-center">
<?= $row['produk_id'] ?>
</td>

<td>
<?= htmlspecialchars($row['nama_produk']) ?>
</td>

<td>
<?= htmlspecialchars($row['nama_kategori']) ?>
</td>

<td>
Rp <?= number_format($row['harga'],0,',','.') ?>
</td>

<td class="text-center">

<?php if($row['stok'] < 10): ?>

<span class="badge bg-danger">
<?= $row['stok'] ?>
</span>

<?php else: ?>

<span class="badge bg-success">
<?= $row['stok'] ?>
</span>

<?php endif; ?>

</td>

<td class="text-center">

<?php if (!empty($row['gambar_url'])): ?>

<img
src="../<?= htmlspecialchars($row['gambar_url']) ?>"
class="img-thumbnail"
style="width:60px;height:60px;object-fit:cover;"
>

<?php else: ?>

Tidak ada

<?php endif; ?>

</td>

<td class="text-center">

<a
href="?page=produk&edit=<?= $row['produk_id'] ?>"
class="btn btn-warning btn-sm"
>
Edit
</a>

<a
href="?page=produk&hapus=<?= $row['produk_id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin hapus produk ini?')"
>
Hapus
</a>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="7" class="text-center">
Belum ada produk
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>
</div>

</div>

<!-- ==================== EDIT PRODUK ==================== -->

<?php

if (isset($_GET['edit'])):

$produk_id_edit = (int)$_GET['edit'];

$dataEdit = $conn
->query("SELECT * FROM produk WHERE produk_id='$produk_id_edit'")
->fetch_assoc();

if ($dataEdit):

?>

<div class="modal show">

<div class="modal-dialog modal-lg">

<div class="modal-content text-dark">

<form method="POST">

<div class="modal-header bg-warning">

<h5 class="modal-title">
Edit Produk
</h5>

<a href="?page=produk" class="btn-close"></a>

</div>

<div class="modal-body">

<input
type="hidden"
name="produk_id"
value="<?= $dataEdit['produk_id'] ?>"
>

<div class="mb-3">

<label class="form-label">
Nama Produk
</label>

<input
type="text"
name="nama_produk"
class="form-control"
value="<?= htmlspecialchars($dataEdit['nama_produk']) ?>"
required
>

</div>

<div class="mb-3">

<label class="form-label">
Pilih Gambar Baru
</label>

<select
name="gambar_url"
class="form-select"
onchange="previewImage(this, 'previewEdit')"
>

<option value="">
-- Pilih Gambar --
</option>

<?php

$files = glob("../foto/*.{jpg,jpeg,png,webp}", GLOB_BRACE);

if ($files) {

  foreach ($files as $file) {

    $nama_file = basename($file);

    $path_db = "foto/" . $nama_file;

    $selected =
      ($path_db == $dataEdit['gambar_url'])
      ? 'selected'
      : '';

    echo "
    <option value='$path_db' $selected>
      $nama_file
    </option>
    ";
  }
}

?>

</select>

</div>

<div class="text-center">

<img
id="previewEdit"
src="../<?= htmlspecialchars($dataEdit['gambar_url']) ?>"
class="preview-img"
>

</div>

</div>

<div class="modal-footer">

<a href="?page=produk" class="btn btn-secondary">
Batal
</a>

<button
type="submit"
name="update"
class="btn btn-warning"
>
Simpan
</button>

</div>

</form>

</div>
</div>
</div>

<?php endif; endif; ?>

<?php $conn->close(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

// ==================== PREVIEW GAMBAR ====================

function previewImage(selectElement, previewId){

    const previewImg = document.getElementById(previewId);

    let imagePath = selectElement.value;

    if(imagePath !== ""){

        previewImg.src = "../" + imagePath;

    }else{

        previewImg.src = "https://placehold.co/120x120?text=No+Image";

    }
}

// ==================== SMOOTH SCROLL ====================

window.addEventListener('wheel', function(e){

    e.preventDefault();

    window.scrollBy({
        top: e.deltaY * 1.2,
        behavior: 'smooth'
    });

}, { passive:false });

</script>

</body>
</html>