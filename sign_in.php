<?php
// Memulai session untuk menangkap error flash message
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("config/database.php");

/* ================= INSERT / REGISTER USER ================= */
if (isset($_POST['action']) && $_POST['action'] == 'insert') {

    // VALIDASI TERMS
    if (!isset($_POST['terms'])) {

        $_SESSION['register_error'] = "Anda harus menyetujui syarat & ketentuan!";

    } else {

        $nama     = $conn->real_escape_string($_POST['nama']);
        $email    = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $alamat   = $conn->real_escape_string($_POST['alamat']);
        $no_hp    = $conn->real_escape_string($_POST['no_hp']);

        // CEK DUPLIKAT EMAIL
        $cek_email = $conn->query("SELECT email FROM user WHERE email='$email'");

        if ($cek_email->num_rows > 0) {

            $_SESSION['register_error'] = "Email sudah terdaftar, gunakan email lain!";

        } else {

            $insert_query = "INSERT INTO user (nama, email, password, alamat, no_hp)
                             VALUES ('$nama', '$email', '$password', '$alamat', '$no_hp')";

            if ($conn->query($insert_query)) {

                $_SESSION['register_success'] = "Akun berhasil dibuat, silakan login!";

                header("Location: login.php");
                exit;

            } else {

                $_SESSION['register_error'] = "Gagal mendaftarkan user: " . $conn->error;
            }
        }
    }
}

/* ================= UPDATE USER ================= */
if (isset($_POST['action']) && $_POST['action'] == 'update') {

    $id     = (int) $_POST['user_id'];
    $nama   = $conn->real_escape_string($_POST['nama']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $no_hp  = $conn->real_escape_string($_POST['no_hp']);

    $update_query = "UPDATE user 
                     SET nama='$nama', alamat='$alamat', no_hp='$no_hp' 
                     WHERE user_id=$id";

    if ($conn->query($update_query)) {

        $_SESSION['register_success'] = "Data user berhasil diperbarui!";

        header("Location: dashboard.php");
        exit;

    } else {

        $_SESSION['register_error'] = "Gagal memperbarui data user!";
    }
}

/* ================= DELETE USER ================= */
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    $conn->query("DELETE FROM user WHERE user_id=$id");

    header("Location: dashboard.php");
    exit;
}

/* ================= MODE EDIT ================= */
$is_edit = false;
$dataEdit = [];

if (isset($_GET['edit'])) {

    $is_edit = true;

    $id_edit = (int)$_GET['edit'];

    $result_edit = $conn->query("SELECT * FROM user WHERE user_id=$id_edit");

    if ($result_edit->num_rows > 0) {

        $dataEdit = $result_edit->fetch_assoc();

    } else {

        $is_edit = false;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= $is_edit ? 'Edit Akun' : 'Daftar' ?> — ThriftPay</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
  min-height:100vh;
  background:#f8f7f4;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:2rem 0;
  font-family:'Segoe UI',system-ui,sans-serif;
}

.auth-wrapper{
  display:flex;
  width:min(960px,95vw);
  border-radius:20px;
  overflow:hidden;
  box-shadow:0 8px 40px rgba(0,0,0,.16);
}

.auth-side{
  flex:0 0 300px;
  background:linear-gradient(145deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  padding:3rem 2rem;
  color:#fff;
}

.auth-side img{
  width:140px;
  margin-bottom:1.5rem;
}

.auth-side h2{
  font-size:1.4rem;
  font-weight:700;
  text-align:center;
}

.auth-side p{
  font-size:.85rem;
  text-align:center;
  color:rgba(255,255,255,.7);
}

.auth-form-box{
  flex:1;
  background:#fff;
  padding:2.5rem;
}

.auth-form-box h3{
  font-size:1.5rem;
  font-weight:700;
}

.auth-sub{
  color:#6b7280;
  font-size:.85rem;
  margin-bottom:1.5rem;
}

.form-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:1rem;
}

.full{
  grid-column:1 / -1;
}

.form-group{
  margin-bottom:1rem;
}

.form-group label{
  display:block;
  font-size:.82rem;
  font-weight:600;
  margin-bottom:.4rem;
}

.required-star{
  color:#e94560;
}

.input-icon{
  position:relative;
}

.input-icon .bi:not(.toggle-pw){
  position:absolute;
  left:.85rem;
  top:50%;
  transform:translateY(-50%);
  color:#9ca3af;
}

.textarea-wrap .bi{
  top:.8rem !important;
  transform:none !important;
}

.input-icon input,
.input-icon textarea{
  width:100%;
  padding:.7rem .85rem .7rem 2.5rem;
  border:1.5px solid #e5e7eb;
  border-radius:10px;
  background:#f9fafb;
  outline:none;
}

.input-icon input:focus,
.input-icon textarea:focus{
  border-color:#e94560;
  background:#fff;
}

textarea{
  resize:vertical;
  min-height:90px;
}

.toggle-pw{
  position:absolute;
  right:.85rem;
  top:50%;
  transform:translateY(-50%);
  cursor:pointer;
  color:#9ca3af;
}

.has-toggle{
  padding-right:2.5rem !important;
}

.pw-hint{
  font-size:.74rem;
  margin-top:.3rem;
}

.pw-strength{
  margin-top:.4rem;
  height:4px;
  background:#e5e7eb;
  border-radius:4px;
  overflow:hidden;
}

.pw-strength-bar{
  height:100%;
  width:0;
  transition:.3s;
}

.auth-error{
  background:#fef2f2;
  border:1px solid #fecaca;
  color:#dc2626;
  border-radius:8px;
  padding:.7rem;
  margin-bottom:1rem;
}

.terms-check{
  display:flex;
  gap:.5rem;
  margin-top:1rem;
  margin-bottom:1rem;
}

.terms-check input{
  accent-color:#e94560;
}

.terms-check label{
  font-size:.82rem;
}

.btn-auth{
  width:100%;
  padding:.8rem;
  border:none;
  border-radius:10px;
  background:linear-gradient(135deg,#e94560,#c73652);
  color:#fff;
  font-weight:600;
}

.auth-divider{
  margin:1.2rem 0;
  text-align:center;
  color:#9ca3af;
}

.auth-switch{
  text-align:center;
}

.auth-switch a{
  color:#e94560;
  text-decoration:none;
  font-weight:600;
}

@media(max-width:700px){

  .auth-wrapper{
    flex-direction:column;
  }

  .form-grid{
    grid-template-columns:1fr;
  }

}
</style>

</head>
<body>

<div class="auth-wrapper">

  <div class="auth-side">

    <img src="foto/ThriftPay.png" alt="Logo">

    <h2>
      <?= $is_edit ? 'Perbarui Data' : 'Bergabung Sekarang' ?>
    </h2>

    <p>
      <?= $is_edit
      ? 'Kelola pembaharuan profil user.'
      : 'Buat akun dan nikmati pengalaman thrift terbaik.' ?>
    </p>

  </div>

  <div class="auth-form-box">

    <h3><?= $is_edit ? 'Edit Akun User' : 'Buat Akun' ?></h3>

    <p class="auth-sub">
      Kolom 
      <span class="required-star"></span>
      wajib diisi.
    </p>

    <?php if (!empty($_SESSION['register_error'])): ?>

      <div class="auth-error">
        <?= htmlspecialchars($_SESSION['register_error']) ?>
      </div>

      <?php unset($_SESSION['register_error']); ?>

    <?php endif; ?>

    <form action="" method="POST" id="registerForm">

      <input type="hidden" name="action" value="<?= $is_edit ? 'update' : 'insert' ?>">

      <?php if ($is_edit): ?>
        <input type="hidden" name="user_id" value="<?= $dataEdit['user_id'] ?>">
      <?php endif; ?>

      <div class="form-grid">

        <!-- NAMA -->
        <div class="form-group full">

          <label for="nama">
            Nama Lengkap <span class="required-star">*</span>
          </label>

          <div class="input-icon">

            <i class="bi bi-person"></i>

            <input
              type="text"
              id="nama"
              name="nama"
              placeholder="Nama lengkap kamu"
              maxlength="100"
              required
              autocomplete="name"
              value="<?= htmlspecialchars($is_edit ? $dataEdit['nama'] : ($_POST['nama'] ?? '')) ?>"
            >

          </div>
        </div>

        <!-- EMAIL -->
        <div class="form-group full">

          <label for="email">
            Email <span class="required-star">*</span>
          </label>

          <div class="input-icon">

            <i class="bi bi-envelope"></i>

            <input
              type="email"
              id="email"
              name="email"
              placeholder="contoh@email.com"
              maxlength="100"
              required
              autocomplete="email"
              value="<?= htmlspecialchars($is_edit ? $dataEdit['email'] : ($_POST['email'] ?? '')) ?>"
              <?= $is_edit ? 'disabled style="background:#e9ecef;cursor:not-allowed;"' : '' ?>
            >

          </div>
        </div>

        <!-- NO HP -->
        <div class="form-group">

          <label for="no_hp">
            No. HP <span class="required-star">*</span>
          </label>

          <div class="input-icon">

            <i class="bi bi-telephone"></i>

            <input
              type="tel"
              id="no_hp"
              name="no_hp"
              placeholder="08xxxxxxxxxx"
              maxlength="15"
              autocomplete="tel"
              required
              value="<?= htmlspecialchars($is_edit ? $dataEdit['no_hp'] : ($_POST['no_hp'] ?? '')) ?>"
            >

          </div>
        </div>

        <!-- ROLE -->
        <input type="hidden" name="role" value="customer">

        <!-- ALAMAT -->
        <div class="form-group full">

          <label for="alamat">
            Alamat <span class="required-star">*</span>
          </label>

          <div class="input-icon textarea-wrap">

            <i class="bi bi-geo-alt"></i>

            <textarea
              id="alamat"
              name="alamat"
              placeholder="Jalan, Kota, Provinsi..."
              rows="3"
              required
            ><?= htmlspecialchars($is_edit ? $dataEdit['alamat'] : ($_POST['alamat'] ?? '')) ?></textarea>

          </div>
        </div>

        <?php if (!$is_edit): ?>

        <!-- PASSWORD -->
        <div class="form-group">

          <label for="password">
            Password <span class="required-star">*</span>
          </label>

          <div class="input-icon">

            <i class="bi bi-lock"></i>

            <input
              type="password"
              id="password"
              name="password"
              placeholder="Min. 8 karakter"
              class="has-toggle"
              required
              minlength="8"
              autocomplete="new-password"
            >

            <i class="bi bi-eye toggle-pw" id="togglePw1"></i>

          </div>

          <div class="pw-strength">
            <div class="pw-strength-bar" id="pwBar"></div>
          </div>

          <div class="pw-hint" id="pwHint">
            Minimal 8 karakter
          </div>

        </div>

        <!-- KONFIRMASI PASSWORD -->
        <div class="form-group">

          <label for="confirm_password">
            Konfirmasi Password <span class="required-star">*</span>
          </label>

          <div class="input-icon">

            <i class="bi bi-lock-fill"></i>

            <input
              type="password"
              id="confirm_password"
              name="confirm_password"
              placeholder="Ulangi password"
              class="has-toggle"
              required
              autocomplete="new-password"
            >

            <i class="bi bi-eye toggle-pw" id="togglePw2"></i>

          </div>

          <div class="pw-hint" id="matchHint"></div>

        </div>

        <?php endif; ?>

      </div>

      <!-- TERMS -->
      <div class="terms-check">

        <input
          type="checkbox"
          id="terms"
          name="terms"
          required
        >

        <label for="terms">
          Saya menyetujui Syarat & Ketentuan serta Kebijakan Privasi ThriftPay.
        </label>

      </div>

      <!-- BUTTON -->
      <button type="submit" class="btn-auth">

        <i class="bi <?= $is_edit ? 'bi-check-lg' : 'bi-person-plus' ?> me-1"></i>

        <?= $is_edit ? 'Simpan Perubahan' : 'Buat Akun' ?>

      </button>

    </form>

    <div class="auth-divider">
    </div>

    <div class="auth-switch">

      <a href="login.php">
        <i class="bi bi-arrow-left me-1"></i>
        Dashboard Admin
      </a>

    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* TOGGLE PASSWORD */
function togglePassword(toggleId,inputId){

  const btn=document.getElementById(toggleId);
  const input=document.getElementById(inputId);

  if(!btn || !input) return;

  btn.addEventListener('click',()=>{

    const show=input.type==='password';

    input.type=show ? 'text' : 'password';

    btn.classList.toggle('bi-eye');
    btn.classList.toggle('bi-eye-slash');

  });
}

togglePassword('togglePw1','password');
togglePassword('togglePw2','confirm_password');

/* PASSWORD CHECK */
const pwInput=document.getElementById('password');
const pwBar=document.getElementById('pwBar');
const pwHint=document.getElementById('pwHint');

const confirmInput=document.getElementById('confirm_password');
const matchHint=document.getElementById('matchHint');

if(pwInput){

  pwInput.addEventListener('input',()=>{

    const val=pwInput.value;

    let score=0;

    if(val.length >= 8) score++;
    if(/[A-Z]/.test(val)) score++;
    if(/[0-9]/.test(val)) score++;
    if(/[^A-Za-z0-9]/.test(val)) score++;

    const levels=[
      {w:'0%',bg:'#e5e7eb',text:'Minimal 8 karakter'},
      {w:'30%',bg:'#ef4444',text:'Lemah'},
      {w:'55%',bg:'#f59e0b',text:'Sedang'},
      {w:'80%',bg:'#3b82f6',text:'Kuat'},
      {w:'100%',bg:'#10b981',text:'Sangat kuat ✓'}
    ];

    const lv=levels[score];

    pwBar.style.width=lv.w;
    pwBar.style.background=lv.bg;

    pwHint.textContent=lv.text;
    pwHint.style.color=lv.bg;

  });
}

if(confirmInput){

  confirmInput.addEventListener('input',()=>{

    if(confirmInput.value===''){

      matchHint.textContent='';
      return;
    }

    const match=confirmInput.value===pwInput.value;

    matchHint.textContent=match
      ? 'Password cocok ✓'
      : 'Password tidak cocok';

    matchHint.style.color=match
      ? '#10b981'
      : '#ef4444';

  });
}

/* VALIDASI FORM */
document.getElementById('registerForm').addEventListener('submit',(e)=>{

  const terms=document.getElementById('terms');

  // VALIDASI TERMS
  if(!terms.checked){

    e.preventDefault();

    alert('Anda harus menyetujui syarat & ketentuan!');

    return;
  }

  // VALIDASI PASSWORD
  if(confirmInput && pwInput){

    if(confirmInput.value !== pwInput.value){

      e.preventDefault();

      matchHint.textContent='Password tidak cocok!';
      matchHint.style.color='#ef4444';

      confirmInput.focus();
    }
  }

});

</script>

</body>
</html>