<?php
include_once("../config/database.php");

// ========================================
// HAPUS DATA
// ========================================
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  
  $stmt = $conn->prepare("DELETE FROM kategori WHERE kategori_id = ?");
  $stmt->bind_param("i", $id);
  
  if ($stmt->execute()) {
    echo "<script>alert('Kategori berhasil dihapus!'); window.location='?page=kategori';</script>";
  } else {
    echo "<script>alert('Gagal menghapus kategori karena terikat data produk!'); window.location='?page=kategori';</script>";
  }
  $stmt->close();
}

// ========================================
// UPDATE DATA
// ========================================
if (isset($_POST['update'])) {
  $id = intval($_POST['kategori_id']);
  $nama = $conn->real_escape_string($_POST['nama_kategori']);
  $desk = $conn->real_escape_string($_POST['deskripsi']);

  $stmt = $conn->prepare("UPDATE kategori SET nama_kategori = ?, deskripsi = ? WHERE kategori_id = ?");
  $stmt->bind_param("ssi", $nama, $desk, $id);
  
  if ($stmt->execute()) {
    echo "<script>alert('Kategori berhasil diperbarui!'); window.location='?page=kategori';</script>";
  } else {
    echo "<script>alert('Gagal update kategori');</script>";
  }
  $stmt->close();
}

// ========================================
// TAMBAH DATA BARU
// ========================================
if (isset($_POST['simpan'])) {
  $nama = $conn->real_escape_string($_POST['nama_kategori']);
  $desk = $conn->real_escape_string($_POST['deskripsi']);

  $cek = $conn->query("SELECT * FROM kategori WHERE nama_kategori='$nama'");
  if ($cek->num_rows > 0) {
    echo "<script>alert('Nama kategori sudah ada!');</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $desk);
    
    if ($stmt->execute()) {
      echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='?page=kategori';</script>";
    } else {
      echo "<script>alert('Gagal menambahkan kategori');</script>";
    }
    $stmt->close();
  }
}

// ========================================
// AMBIL DATA KATEGORI
// ========================================
$result = $conn->query("SELECT * FROM kategori ORDER BY kategori_id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Kategori - Triftypay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .form-thrift input, .form-thrift textarea {
      background-color: #ffffff;
      border: 1px solid #cccccc;
      color: var(--text-dark) !important;
      border-radius: 0;
      padding: 10px 15px;
    }
    .form-thrift input:focus, .form-thrift textarea:focus {
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

    /* Modal Light Custom */
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
    <h2 class="dashboard-title m-0">Kategori Produk</h2>
    <span class="badge badge-count font-monospace">
      <i class="bi bi-tags-fill me-1"></i> TOTAL: <?= $result->num_rows ?> KATEGORI
    </span>
  </div>

  <div class="row g-4">
    <!-- FORM TAMBAH DATA (Sisi Kiri) -->
    <div class="col-xl-4 col-lg-5">
      <div class="card card-thrift shadow-sm">
        <div class="card-thrift-header">
          <i class="bi bi-plus-circle-fill me-2"></i>Tambah Kategori Baru
        </div>
        <div class="card-body p-4">
          <form method="POST" class="form-thrift">
            <div class="mb-3">
              <label>Nama Kategori</label>
              <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Crewneck, Hoodie" required>
            </div>
            <div class="mb-4">
              <label>Deskripsi Kategori</label>
              <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tulis rincian singkat kategori..."></textarea>
            </div>
            <button type="submit" name="simpan" class="btn btn-save-thrift w-100">
              <i class="bi bi-floppy-fill me-1"></i> Simpan Kategori
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- TABEL DATA KATEGORI (Sisi Kanan) -->
    <div class="col-xl-8 col-lg-7">
      <div class="card card-thrift shadow-sm">
        <div class="card-thrift-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-collection-fill me-2"></i>Database List Kategori</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-thrift table-hover align-middle">
              <thead>
                <tr>
                  <th width="10%" class="text-center">No</th>
                  <th width="30%">Nama Kategori</th>
                  <th width="40%">Deskripsi</th>
                  <th width="20%" class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                if ($result && $result->num_rows > 0):
                  while ($row = $result->fetch_assoc()):
                ?>
                  <tr>
                    <td class="text-center font-monospace text-secondary fw-bold">#<?= $no; ?></td>
                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_kategori']); ?></td>
                    <td class="text-secondary small"><?= htmlspecialchars($row['deskripsi'] ?: '-'); ?></td>
                    <td class="text-center">
                      <div class="d-inline-flex gap-2">
                        <a href="?page=kategori&edit=<?= $row['kategori_id']; ?>" class="btn btn-action-outline">
                          <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="?page=kategori&hapus=<?= $row['kategori_id']; ?>" class="btn btn-action-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                          <i class="bi bi-trash3"></i> Hapus
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php 
                  $no++;
                  endwhile;
                else: 
                ?>
                  <tr>
                    <td colspan="4" class="text-center text-muted py-5 font-monospace">
                      <i class="bi bi-folder-x d-block fs-3 mb-2"></i> Belum ada data kategori tersimpan.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FORM EDIT MODAL POPUP -->
<?php 
if (isset($_GET['edit'])) {
  $idEdit = intval($_GET['edit']);
  $dataEdit = $conn->query("SELECT * FROM kategori WHERE kategori_id=$idEdit")->fetch_assoc();
  if ($dataEdit) {
?>
<div class="modal fade show" style="display:block; background-color:rgba(255,255,255,0.5); backdrop-filter: blur(4px); z-index: 1050;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-thrift-content shadow-lg">
      <form method="POST" class="form-thrift">
        <input type="hidden" name="kategori_id" value="<?= $dataEdit['kategori_id'] ?>">
        
        <div class="modal-header modal-thrift-header">
          <h5 class="modal-title fw-bold text-uppercase" style="font-size:0.9rem; letter-spacing:0.5px;">
            <i class="bi bi-pencil-square me-2"></i>Edit Kategori
          </h5>
        </div>
        
        <div class="modal-body p-4">
          <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($dataEdit['nama_kategori']) ?>" required>
          </div>
          <div class="mb-2">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($dataEdit['deskripsi']) ?></textarea>
          </div>
        </div>
        
        <div class="modal-footer border-0 pt-0 px-4 pb-4">
          <a href="?page=kategori" class="btn btn-action-outline px-4 py-2 me-2">Batal</a>
          <button type="submit" name="update" class="btn btn-save-thrift px-4 py-2">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php 
  }
} 
?>

</body>
</html>

<?php $conn->close(); ?>