<?php
// ==== KONEKSI DATABASE ====
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "websitekatalogbaju";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// ==== HAPUS USER ====
if (isset($_GET['hapus'])) {
  $email = $_GET['hapus'];
  $conn->query("DELETE FROM user WHERE email='$email'");
  echo "<script>alert('User berhasil dihapus!'); window.location='../admin-page/dashboard.php';</script>";
}

// ==== PROSES UPDATE USER ====
if (isset($_POST['update'])) {
  $email = $_POST['email'];
  $nama = $conn->real_escape_string($_POST['nama']);
  $alamat = $conn->real_escape_string($_POST['alamat']);
  $no_hp = $conn->real_escape_string($_POST['no_hp']);

  $sql = "UPDATE user SET nama='$nama', alamat='$alamat', no_hp='$no_hp' WHERE email='$email'";
  if ($conn->query($sql)) {
    echo "<script>alert('Data user berhasil diupdate!'); window.location='../admin-page/dashboard.php';</script>";
  } else {
    echo "<script>alert('Gagal update user');</script>";
  }
}

// ==== TAMBAH USER BARU ====
if (isset($_POST['simpan'])) {
  $nama = $conn->real_escape_string($_POST['nama']);
  $email = $conn->real_escape_string($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $alamat = $conn->real_escape_string($_POST['alamat']);
  $no_hp = $conn->real_escape_string($_POST['no_hp']);

  $cek = $conn->query("SELECT email FROM user WHERE email='$email'");
  if ($cek->num_rows > 0) {
    echo "<script>alert('Email sudah terdaftar, gunakan email lain!');</script>";
  } else {
    $sql = "INSERT INTO user (nama, email, password, alamat, no_hp) 
            VALUES ('$nama', '$email', '$password', '$alamat', '$no_hp')";
    if ($conn->query($sql) === TRUE) {
    echo "<script>alert('User berhasil ditambahkan!'); window.location='../admin-page/dashboard.php';</script>";

    } else {
      echo "<script>alert('Gagal menambahkan user: " . addslashes($conn->error) . "');</script>";
    }
  }
}

// ==== AMBIL DATA USER ====
$result = $conn->query("SELECT * FROM user ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <h2 class="text-center mb-4">Dashboard Admin - Website Katalog Baju</h2>

  <!-- Form Tambah User -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Tambah User Baru</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-4">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label>No HP</label>
            <input type="text" name="no_hp" class="form-control" required>
          </div>
        </div>
        <div class="mt-3 text-end">
          <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabel Data User -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Data User</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-secondary">
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Alamat</th>
            <th>No HP</th>
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
                      <td>{$row['nama']}</td>
                      <td>{$row['email']}</td>
                      <td>{$row['alamat']}</td>
                      <td>{$row['no_hp']}</td>
                      <td>
                        <a href='?edit={$row['email']}' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='?hapus={$row['email']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus user ini?\")'>Hapus</a>
                      </td>
                    </tr>";
              $no++;
            }
          } else {
            echo "<tr><td colspan='6' class='text-center'>Belum ada data user</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Edit User -->
<?php 
if (isset($_GET['edit'])) {
  $emailEdit = $_GET['edit'];
  $dataEdit = $conn->query("SELECT * FROM user WHERE email='$emailEdit'")->fetch_assoc();
?>
<div class="modal fade show" style="display:block; background-color:rgba(0,0,0,0.5)">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Edit User</h5>
        </div>
        <div class="modal-body">
          <input type="hidden" name="email" value="<?= $dataEdit['email'] ?>">
          <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= $dataEdit['nama'] ?>" required>
          </div>
          <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" value="<?= $dataEdit['alamat'] ?>" required>
          </div>
          <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="no_hp" class="form-control" value="<?= $dataEdit['no_hp'] ?>" required>
          </div>
        </div>
        <div class="modal-footer">
          <a href="" class="btn btn-secondary">Batal</a>
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
