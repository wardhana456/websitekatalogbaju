<?php
// ===== KONEKSI DATABASE =====
$host = "localhost";
$user = "root";
$pass = "";
$db   = "websitekatalogbaju";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// ===== HAPUS DATA =====
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM kategori WHERE kategori_id=$id");
  echo "<script>alert('Kategori berhasil dihapus!'); window.location='?page=kategori';</script>";
}

// ===== UPDATE DATA =====
if (isset($_POST['update'])) {
  $id = intval($_POST['kategori_id']);
  $nama = $conn->real_escape_string($_POST['nama_kategori']);
  $desk = $conn->real_escape_string($_POST['deskripsi']);

  $sql = "UPDATE kategori SET nama_kategori='$nama', deskripsi='$desk' WHERE kategori_id=$id";
  if ($conn->query($sql)) {
    echo "<script>alert('Kategori berhasil diperbarui!'); window.location='?page=kategori';</script>";
  } else {
    echo "<script>alert('Gagal update kategori');</script>";
  }
}

// ===== TAMBAH DATA BARU =====
if (isset($_POST['simpan'])) {
  $nama = $conn->real_escape_string($_POST['nama_kategori']);
  $desk = $conn->real_escape_string($_POST['deskripsi']);

  $cek = $conn->query("SELECT * FROM kategori WHERE nama_kategori='$nama'");
  if ($cek->num_rows > 0) {
    echo "<script>alert('Nama kategori sudah ada!');</script>";
  } else {
    $sql = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama', '$desk')";
    if ($conn->query($sql)) {
      echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='?page=kategori';</script>";
    } else {
      echo "<script>alert('Gagal menambahkan kategori');</script>";
    }
  }
}

// ===== AMBIL DATA KATEGORI =====
$result = $conn->query("SELECT * FROM kategori ORDER BY kategori_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Kategori - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center mb-4">📦 Kelola Kategori Produk</h2>

  <!-- Form Tambah -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Kategori</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Deskripsi</label>
            <input type="text" name="deskripsi" class="form-control">
          </div>
        </div>
        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">➕ Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabel Data -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Data Kategori</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$no}</td>
                      <td>{$row['nama_kategori']}</td>
                      <td>{$row['deskripsi']}</td>
                      <td>
                        <a href='?page=kategori&edit={$row['kategori_id']}' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='?page=kategori&hapus={$row['kategori_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>
                      </td>
                    </tr>";
              $no++;
            }
          } else {
            echo "<tr><td colspan='4' class='text-center'>Belum ada kategori</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Form Edit -->
<?php 
if (isset($_GET['edit'])) {
  $idEdit = intval($_GET['edit']);
  $dataEdit = $conn->query("SELECT * FROM kategori WHERE kategori_id=$idEdit")->fetch_assoc();
?>
<div class="modal fade show" style="display:block; background-color:rgba(0,0,0,0.5)">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Edit Kategori</h5>
        </div>
        <div class="modal-body">
          <input type="hidden" name="kategori_id" value="<?= $dataEdit['kategori_id'] ?>">
          <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($dataEdit['nama_kategori']) ?>" required>
          </div>
          <div class="mb-3">
            <label>Deskripsi</label>
            <input type="text" name="deskripsi" class="form-control" value="<?= htmlspecialchars($dataEdit['deskripsi']) ?>">
          </div>
        </div>
        <div class="modal-footer">
          <a href="?page=kategori" class="btn btn-secondary">Batal</a>
          <button type="submit" name="update" class="btn btn-warning">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>

</body>
</html>

<?php $conn->close(); ?>
