<?php
include_once("../config/database.php");

// ==================== HAPUS USER ====================
if (isset($_GET['hapus'])) {
    $user_id = (int) $_GET['hapus'];
    $conn->query("DELETE FROM user WHERE user_id = $user_id");
    echo "<script>alert('User berhasil dihapus!'); window.location='?page=dashboard';</script>";
}

// ==================== UPDATE USER ====================
if (isset($_POST['update'])) {
    $user_id = (int) $_POST['user_id'];
    $nama    = $conn->real_escape_string($_POST['nama']);
    $alamat  = $conn->real_escape_string($_POST['alamat']);
    $no_hp   = $conn->real_escape_string($_POST['no_hp']);

    $sql = "UPDATE user SET 
                nama   = '$nama',
                alamat = '$alamat',
                no_hp  = '$no_hp'
            WHERE user_id = $user_id";

    if ($conn->query($sql)) {
        echo "<script>alert('Data user berhasil diupdate!'); window.location='?page=dashboard';</script>";
    } else {
        echo "<script>alert('Gagal update user');</script>";
    }
}

// ==================== TAMBAH USER BARU ====================
if (isset($_POST['simpan'])) {
    $nama     = $conn->real_escape_string($_POST['nama']);
    $email    = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat   = $conn->real_escape_string($_POST['alamat']);
    $no_hp    = $conn->real_escape_string($_POST['no_hp']);

    // Cek email agar tidak duplikat
    $cek = $conn->query("SELECT email FROM user WHERE email = '$email'");
    if ($cek->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar, gunakan email lain!');</script>";
    } else {
        $sql = "INSERT INTO user (nama, email, password, alamat, no_hp)
                VALUES ('$nama', '$email', '$password', '$alamat', '$no_hp')";
        
        if ($conn->query($sql)) {
            $last_id = $conn->insert_id;
            echo "<script>alert('User berhasil ditambahkan! ID User: $last_id'); window.location='?page=dashboard';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan user: " . addslashes($conn->error) . "');</script>";
        }
    }
}

// ==================== AMBIL DATA USER ====================
$result = $conn->query("SELECT * FROM user ORDER BY user_id DESC");
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
    <h2 class="text-center mb-4 text-white">Dashboard Admin - Website Katalog Baju</h2>

    <!-- FORM TAMBAH USER -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">Tambah User Baru</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" name="simpan" class="btn btn-success">➕ Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DATA USER -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Data User</div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-secondary text-center">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">Nama</th>
                        <th width="20%">Email</th>
                        <th width="25%">Alamat</th>
                        <th width="15%">No HP</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $row['user_id'] ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                <td class="text-center">
                                    <a href="?edit=<?= $row['user_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?hapus=<?= $row['user_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data user</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDIT USER -->
<?php 
if (isset($_GET['edit'])):
    $user_id  = (int) $_GET['edit'];
    $dataEdit = $conn->query("SELECT * FROM user WHERE user_id = $user_id")->fetch_assoc();
?>
<div class="modal fade show" style="display:block; background-color:rgba(0,0,0,0.5)">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit User</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $dataEdit['user_id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($dataEdit['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($dataEdit['alamat']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($dataEdit['no_hp']) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="?page=dashboard" class="btn btn-secondary">Batal</a>
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
