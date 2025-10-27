<?php
include_once("../config/database.php");

// ===== Ambil daftar user untuk dropdown =====
$user_result = $conn->query("SELECT user_id, nama FROM user ORDER BY user_id ASC");

// ===== Ambil daftar produk untuk dropdown =====
$produk_result = $conn->query("SELECT produk_id, nama_produk FROM produk ORDER BY produk_id ASC");

// ===== Hapus Review =====
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM review WHERE review_id=$id");
  echo "<script>alert('Review berhasil dihapus!'); window.location='?page=review';</script>";
  exit;
}

// ===== Tambah Review =====
if (isset($_POST['simpan'])) {
  $user_id = intval($_POST['user_id']);
  $produk_id = intval($_POST['produk_id']);
  $rating = intval($_POST['rating']);
  $komentar = $conn->real_escape_string($_POST['komentar']);

  $sql = "INSERT INTO review (user_id, produk_id, rating, komentar) 
          VALUES ('$user_id', '$produk_id', '$rating', '$komentar')";

  if ($conn->query($sql)) {
    echo "<script>alert('Review berhasil ditambahkan!'); window.location='?page=review';</script>";
  } else {
    echo "<script>alert('Gagal menambahkan review: " . addslashes($conn->error) . "');</script>";
  }
}

// ===== Ambil Data Review =====
$result = $conn->query("
  SELECT r.*, u.nama AS nama_user, p.nama_produk 
  FROM review r 
  JOIN user u ON r.user_id = u.user_id 
  JOIN produk p ON r.produk_id = p.produk_id 
  ORDER BY r.review_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Review - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center text-white mb-4">Kelola Review</h2>

  <!-- FORM TAMBAH REVIEW -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah Review Baru</div>
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
            <label>Produk</label>
            <select name="produk_id" class="form-select" required>
              <option value="">Pilih Produk</option>
              <?php
              if ($produk_result->num_rows > 0) {
                while ($produk = $produk_result->fetch_assoc()) {
                  echo "<option value='{$produk['produk_id']}'>{$produk['nama_produk']} (ID: {$produk['produk_id']})</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-md-4">
            <label>Rating (1 - 5)</label>
            <select name="rating" class="form-select" required>
              <?php
              for ($i = 1; $i <= 5; $i++) {
                echo "<option value='$i'>$i</option>";
              }
              ?>
            </select>
          </div>
        </div>

        <div class="mt-3">
          <label>Komentar</label>
          <textarea name="komentar" class="form-control" rows="3" placeholder="Tulis komentar review..." required></textarea>
        </div>

        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Review</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TABEL REVIEW -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Data Review</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>ID Review</th>
            <th>Nama User</th>
            <th>Produk</th>
            <th>Rating</th>
            <th>Komentar</th>
            <th>Tanggal Review</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['review_id']}</td>
                      <td>{$row['nama_user']}</td>
                      <td>{$row['nama_produk']}</td>
                      <td>⭐ {$row['rating']}</td>
                      <td>{$row['komentar']}</td>
                      <td>{$row['tanggal_review']}</td>
                      <td>
                        <a href='?page=review&hapus={$row['review_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus review ini?\")'>Hapus</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='7' class='text-center'>Belum ada review</td></tr>";
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
