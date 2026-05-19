<?php
include_once("../config/database.php");


// ========================================
// AMBIL DATA USER
// ========================================
$user_result = $conn->query("
    SELECT user_id, nama 
    FROM user 
    ORDER BY nama ASC
");


// ========================================
// HAPUS ORDER
// ========================================
if (isset($_GET['hapus'])) {

    $id = intval($_GET['hapus']);

    $hapus = $conn->query("
        DELETE FROM orders 
        WHERE order_id = $id
    ");

    if ($hapus) {
        echo "<script>
                alert('Order berhasil dihapus!');
                window.location='?page=order';
              </script>";
    } else {
        echo "<script>
                alert('Gagal hapus order!');
              </script>";
    }
}



// ========================================
// TAMBAH ORDER
// ========================================
if (isset($_POST['simpan'])) {

    $user_id = intval($_POST['user_id']);
    $total_harga = floatval($_POST['total_harga']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "
        INSERT INTO orders 
        (user_id, total_harga, status)
        VALUES
        ('$user_id', '$total_harga', '$status')
    ";

    if ($conn->query($sql)) {

        echo "<script>
                alert('Order berhasil ditambahkan!');
                window.location='?page=order';
              </script>";

    } else {

        echo "<script>
                alert('Gagal tambah order: " . addslashes($conn->error) . "');
              </script>";
    }
}



// ========================================
// AMBIL DATA ORDER
// ========================================
$result = $conn->query("
    SELECT 
        o.order_id,
        o.tanggal_order,
        o.total_harga,
        o.status,
        u.nama
    FROM orders o
    JOIN user u 
        ON o.user_id = u.user_id
    ORDER BY o.order_id DESC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="text-center mb-4">
        Kelola Order
    </h2>


    <!-- FORM TAMBAH ORDER -->
    <div class="card shadow mb-4">

        <div class="card-header bg-primary text-white">
            Tambah Order Baru
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="row g-3">

                    <!-- USER -->
                    <div class="col-md-4">

                        <label class="form-label">
                            User
                        </label>

                        <select name="user_id" class="form-select" required>

                            <option value="">
                                Pilih User
                            </option>

                            <?php
                            while ($user = $user_result->fetch_assoc()) {
                            ?>

                                <option value="<?= $user['user_id']; ?>">

                                    <?= $user['nama']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>



                    <!-- TOTAL HARGA -->
                    <div class="col-md-4">

                        <label class="form-label">
                            Total Harga
                        </label>

                        <input 
                            type="number"
                            step="0.01"
                            name="total_harga"
                            class="form-control"
                            required
                        >

                    </div>



                    <!-- STATUS -->
                    <div class="col-md-4">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select" required>

                            <option value="pending">
                                Pending
                            </option>

                            <option value="dibayar">
                                Dibayar
                            </option>

                            <option value="dikirim">
                                Dikirim
                            </option>

                            <option value="selesai">
                                Selesai
                            </option>

                            <option value="dibatalkan">
                                Dibatalkan
                            </option>

                        </select>

                    </div>

                </div>


                <div class="mt-3 text-end">

                    <button type="submit" name="simpan" class="btn btn-success">

                        Simpan Order

                    </button>

                </div>

            </form>

        </div>

    </div>



    <!-- TABEL ORDER -->
    <div class="card shadow">

        <div class="card-header bg-dark text-white">
            Data Order
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-secondary">

                    <tr>

                        <th>ID</th>
                        <th>Nama User</th>
                        <th>Tanggal Order</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php
                if ($result->num_rows > 0) {

                    while ($row = $result->fetch_assoc()) {
                ?>

                    <tr>

                        <td><?= $row['order_id']; ?></td>

                        <td><?= $row['nama']; ?></td>

                        <td><?= $row['tanggal_order']; ?></td>

                        <td>
                            Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                        </td>

                        <td>

                            <?php
                            $badge = "secondary";

                            if ($row['status'] == 'pending') {
                                $badge = "warning";
                            }

                            elseif ($row['status'] == 'dibayar') {
                                $badge = "primary";
                            }

                            elseif ($row['status'] == 'dikirim') {
                                $badge = "info";
                            }

                            elseif ($row['status'] == 'selesai') {
                                $badge = "success";
                            }

                            elseif ($row['status'] == 'dibatalkan') {
                                $badge = "danger";
                            }
                            ?>

                            <span class="badge bg-<?= $badge; ?>">

                                <?= $row['status']; ?>

                            </span>

                        </td>

                        <td>

                            <a 
                                href="?page=order&hapus=<?= $row['order_id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin hapus order ini?')"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>

                <?php
                    }

                } else {

                    echo "
                        <tr>
                            <td colspan='6' class='text-center'>
                                Belum ada order
                            </td>
                        </tr>
                    ";
                }
                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>

<?php
$conn->close();
?> 