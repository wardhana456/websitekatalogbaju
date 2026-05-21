<?php
// ==========================================
// 1. SISTEM & SESSION PROTEKSI
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ==========================================
// 2. KONEKSI DATABASE DIRECT 
// ==========================================
// Menggunakan include_once jika file sudah ada, 
// atau fallback otomatis jika ingin meload manual.
if (file_exists("config/database.php")) {
    include_once("config/database.php");
} else {
    // Sesuaikan parameter ini dengan local servermu jika config/database.php tidak ditemukan
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "katalog_baju";
    $conn = mysqli_connect($host, $user, $pass, $db);
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// ==========================================
// 3. PROSES UPDATE DATA (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp  = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Validasi input minimal nama tidak boleh kosong
    if (empty($nama)) {
        $error_msg = "Nama lengkap tidak boleh dikosongkan!";
    } else {
        $update_query = "UPDATE user SET nama = '$nama', no_hp = '$no_hp', alamat = '$alamat' WHERE user_id = '$user_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['nama'] = $nama; // Perbarui session nama agar Navbar ikut berubah instan
            $success_msg = "Profil skena kamu berhasil diperbarui!";
        } else {
            $error_msg = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}

// ==========================================
// 4. QUERY AMBIL DATA USER TERBARU
// ==========================================
$queryUser = mysqli_query($conn, "SELECT * FROM user WHERE user_id = '$user_id'");
$dataUser  = mysqli_fetch_assoc($queryUser);

// Fallback jika data user mendadak tidak ditemukan di DB
if (!$dataUser) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Avatar huruf pertama
$firstLetter = strtoupper(substr($dataUser['nama'], 0, 1));
?>

<?php include_once("component/header.php"); ?>

<style>
body {
    background-color: #1a1a2e;
    color: #ffffff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    min-height: 100vh;
    padding-top: 100px;
}

/* Glass Card */
.glass-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 20px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.glass-card:hover {
    border-color: rgba(233, 69, 96, 0.3);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

/* Form */
.form-control-custom {
    background-color: rgba(0, 0, 0, 0.25) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
    border-radius: 12px !important;
    padding: 12px 16px;
}

.form-control-custom:focus {
    border-color: #e94560 !important;
    box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15) !important;
}

/* Button */
.btn-skena {
    background: #e94560;
    color: #fff;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    padding: 12px 30px;
}

.btn-skena:hover {
    background: #ff5e7e;
    transform: translateY(-2px);
}

/* Avatar */
.user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a67b5b, #6f4e37);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
}

/* NAV FIX (anti ketutup klik) */
nav, .navbar {
    z-index: 99999 !important;
}
</style>
</head>
<body>

<!-- ==========================================
     5. NAVBAR LAYOUT
// ========================================== -->
<?php include_once("component/navbar.php"); ?>

<!-- ==========================================
     6. MAIN CONTENT PROFIL (GLASSMORPHISM BOX)
// ========================================== -->
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card glass-card shadow border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Header Judul Menu -->
                    <div class="text-center mb-4">
                        <div class="user-avatar mx-auto mb-3 shadow" style="width: 75px; height: 75px; font-size: 2rem; background: linear-gradient(135deg, #e94560, #1a1a2e); border: 2px solid rgba(255,255,255,0.2);">
                            <?= $firstLetter ?>
                        </div>
                        <h3 class="fw-bold mb-1" style="background: linear-gradient(45deg, #fff, #a6a6a6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            Pengaturan Akun
                        </h3>
                        <p class="text-white-50 small">Sinkronisasi detail data pengiriman pakaian thrift kamu</p>
                    </div>
                    
                    <!-- Alert Status Banner -->
                    <?php if($success_msg): ?>
                        <div class="alert alert-success bg-success bg-opacity-75 text-white border-0 py-3 shadow-sm" style="border-radius:12px;">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $success_msg ?>
                        </div>
                    <?php endif; ?>
                    <?php if($error_msg): ?>
                        <div class="alert alert-danger bg-danger bg-opacity-75 text-white border-0 py-3 shadow-sm" style="border-radius:12px;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error_msg ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulir Update Profil -->
                    <form action="profile.php" method="POST" class="mt-4">
                        <div class="mb-3">
                            <label class="form-label text-white-50 small fw-semibold">Email Pengguna (🔒 Tetap)</label>
                            <input type="email" class="form-control form-control-custom" value="<?= htmlspecialchars($dataUser['email']) ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-white-50 small fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control form-control-custom" value="<?= htmlspecialchars($dataUser['nama']) ?>" required placeholder="Masukkan nama lengkap">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-white-50 small fw-semibold">Nomor WhatsApp / HP</label>
                            <input type="text" name="no_hp" class="form-control form-control-custom" value="<?= htmlspecialchars($dataUser['no_hp'] ?? '') ?>" placeholder="Contoh: 081234567xxx">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-white-50 small fw-semibold">Alamat Lengkap Tujuan Pengiriman</label>
                            <textarea name="alamat" class="form-control form-control-custom" rows="4" placeholder="Tulis nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten dan kode pos..."><?= htmlspecialchars($dataUser['alamat'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" name="update_profile" class="btn btn-skena w-100 w-sm-auto shadow">
                                <i class="bi bi-shield-check me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>


<!-- Bootstrap 5 Bundle JS -->
</html>