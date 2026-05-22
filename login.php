<?php
// Memulai session di bagian paling atas untuk membaca flash message error/sukses
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — ThriftPay</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style/index.css">

  <style>
    /* ── Auth page layout ── */
    body {
      min-height: 100vh;
      background: #f8f7f4;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .auth-wrapper {
      display: flex;
      width: min(900px, 95vw);
      min-height: 540px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(0,0,0,.16);
    }

    /* ── Sisi kiri: ilustrasi / branding ── */
    .auth-side {
      flex: 1;
      background: linear-gradient(145deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 3rem 2.5rem;
      color: #fff;
      position: relative;
      overflow: hidden;
    }

    /* Dekorasi lingkaran di background */
    .auth-side::before,
    .auth-side::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      opacity: .08;
      background: #fff;
    }
    .auth-side::before { width: 280px; height: 280px; top: -80px; right: -80px; }
    .auth-side::after  { width: 200px; height: 200px; bottom: -60px; left: -60px; }

    .auth-side img { width: 160px; margin-bottom: 2rem; position: relative; z-index: 1; }

    .auth-side h2 {
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: .6rem;
      text-align: center;
      position: relative;
      z-index: 1;
    }
    .auth-side p {
      font-size: .9rem;
      color: rgba(255,255,255,.65);
      text-align: center;
      line-height: 1.6;
      position: relative;
      z-index: 1;
    }

    /* Badge accent */
    .auth-badge {
      display: inline-block;
      background: #e94560;
      color: #fff;
      font-size: .75rem;
      font-weight: 700;
      padding: .25rem .75rem;
      border-radius: 999px;
      letter-spacing: .5px;
      margin-bottom: 1.25rem;
      position: relative;
      z-index: 1;
    }

    /* ── Sisi kanan: form ── */
    .auth-form-box {
      flex: 1;
      background: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem 2.5rem;
    }

    .auth-form-box h3 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: .3rem;
    }
    .auth-form-box .auth-sub {
      font-size: .88rem;
      color: #6b7280;
      margin-bottom: 2rem;
    }

    /* Form group */
    .form-group { margin-bottom: 1.1rem; }

    .form-group label {
      display: block;
      font-size: .82rem;
      font-weight: 600;
      color: #374151;
      margin-bottom: .4rem;
      letter-spacing: .2px;
    }

    /* Input dengan icon */
    .input-icon {
      position: relative;
    }
    .input-icon .bi {
      position: absolute;
      left: .9rem;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: .95rem;
      pointer-events: none;
      transition: color .2s;
    }
    .input-icon input {
      width: 100%;
      padding: .65rem .9rem .65rem 2.5rem;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      font-size: .9rem;
      color: #1a1a2e;
      background: #f9fafb;
      transition: border-color .2s, box-shadow .2s, background .2s;
      outline: none;
    }
    .input-icon input:focus {
      border-color: #e94560;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(233,69,96,.12);
    }
    .input-icon input:focus + .bi,
    .input-icon .bi:has(~ input:focus) { color: #e94560; }

    /* Toggle password */
    .input-icon .toggle-pw {
      position: absolute;
      right: .9rem;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: .95rem;
      cursor: pointer;
      pointer-events: all;
      transition: color .2s;
      z-index: 2;
    }
    .input-icon .toggle-pw:hover { color: #e94560; }
    .input-icon input.has-toggle { padding-right: 2.5rem; }

    /* Error message dari PHP */
    .auth-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #dc2626;
      border-radius: 8px;
      padding: .65rem .9rem;
      font-size: .85rem;
      margin-bottom: 1.2rem;
      display: flex;
      align-items: center;
      gap: .5rem;
    }

    /* Success message */
    .auth-success {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      color: #16a34a;
      border-radius: 8px;
      padding: .65rem .9rem;
      font-size: .85rem;
      margin-bottom: 1.2rem;
      display: flex;
      align-items: center;
      gap: .5rem;
    }

    /* Remember + Forgot */
    .auth-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.4rem;
      flex-wrap: wrap;
      gap: .5rem;
    }
    .auth-meta label {
      font-size: .83rem;
      color: #6b7280;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: .35rem;
    }
    .auth-meta input[type="checkbox"] {
      accent-color: #e94560;
      width: 14px;
      height: 14px;
    }
    .auth-meta a {
      font-size: .83rem;
      color: #e94560;
      font-weight: 500;
      text-decoration: none;
      transition: opacity .2s;
    }
    .auth-meta a:hover { opacity: .75; }

    /* Submit button */
    .btn-auth {
      width: 100%;
      padding: .72rem;
      background: linear-gradient(135deg, #e94560, #c73652);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: .95rem;
      font-weight: 600;
      letter-spacing: .3px;
      cursor: pointer;
      transition: opacity .2s, transform .15s, box-shadow .2s;
      box-shadow: 0 4px 14px rgba(233,69,96,.35);
    }
    .btn-auth:hover {
      opacity: .92;
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(233,69,96,.4);
    }
    .btn-auth:active { transform: scale(.98); }

    /* Divider */
    .auth-divider {
      display: flex;
      align-items: center;
      gap: .75rem;
      margin: 1.3rem 0;
      color: #d1d5db;
      font-size: .8rem;
    }
    .auth-divider::before,
    .auth-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }

    /* Link ke register */
    .auth-switch {
      text-align: center;
      font-size: .85rem;
      color: #6b7280;
      margin-top: 1rem;
    }
    .auth-switch a {
      color: #e94560;
      font-weight: 600;
      text-decoration: none;
    }
    .auth-switch a:hover { text-decoration: underline; }

    /* ── Responsive: mobile stack vertikal ── */
    @media (max-width: 640px) {
      .auth-wrapper { flex-direction: column; min-height: unset; border-radius: 16px; }
      .auth-side { padding: 2rem 1.5rem; min-height: 180px; }
      .auth-side img { width: 110px; margin-bottom: 1rem; }
      .auth-side h2 { font-size: 1.2rem; }
      .auth-form-box { padding: 2rem 1.5rem; }
    }
  </style>
</head>
<body>

<div class="auth-wrapper">

  <div class="auth-side">
    <img src="foto/ThriftPay.png" alt="ThriftPay Logo">
    <h2>Selamat Datang Kembali!</h2>
    <p>Masuk ke akunmu dan temukan koleksi thrift terbaik dengan harga yang ramah di kantong.</p>
  </div>

  <div class="auth-form-box">
    <h3>Masuk</h3>
    <p class="auth-sub">Silakan login dengan email dan password kamu</p>

    <?php
    /* ── Tampilkan pesan error dari session (set di proses login) ── */
    if (!empty($_SESSION['login_error'])): ?>
      <div class="auth-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= htmlspecialchars($_SESSION['login_error']) ?>
      </div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <?php
    /* ── Tampilkan pesan sukses setelah register ── */
    if (!empty($_SESSION['register_success'])): ?>
      <div class="auth-success">
        <i class="bi bi-check-circle-fill"></i>
        <?= htmlspecialchars($_SESSION['register_success']) ?>
      </div>
      <?php unset($_SESSION['register_success']); ?>
    <?php endif; ?>

    <form action="proses/proses_login.php" method="POST" novalidate>

      <div class="form-group">
        <label for="email">Email</label>
        <div class="input-icon">
          <i class="bi bi-envelope"></i>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="contoh@email.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            required
            autocomplete="email"
          >
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-icon">
          <i class="bi bi-lock"></i>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan password"
            class="has-toggle"
            required
            autocomplete="current-password"
          >
          <i class="bi bi-eye toggle-pw" id="togglePw" title="Tampilkan password"></i>
        </div>
      </div>

      <div class="auth-meta">
        <label>
          <input type="checkbox" name="remember"> Ingat saya
        </label>
        <a href="lupa_password.php">Lupa password?</a>
      </div>

      <button type="submit" class="btn-auth">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
      </button>

    </form>

    <div class="auth-divider">atau</div>

    <div class="auth-switch">
      Belum punya akun? <a href="sign_in.php">Daftar sekarang</a>
    </div>

  </div></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  /* Toggle show/hide password */
  const togglePw = document.getElementById('togglePw');
  const pwInput  = document.getElementById('password');

  togglePw.addEventListener('click', () => {
    const isHidden = pwInput.type === 'password';
    pwInput.type = isHidden ? 'text' : 'password';
    togglePw.classList.toggle('bi-eye',      !isHidden);
    togglePw.classList.toggle('bi-eye-slash', isHidden);
  });
</script>
</body>
</html>