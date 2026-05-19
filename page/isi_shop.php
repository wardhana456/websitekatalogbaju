<?php /* ===== page/isi_shop.php — Konten halaman Shop (Super Sale) ===== */ ?>

<!-- ========== SUPER SALE GRID ========== -->
<section class="container my-5 reveal">
  <div class="section-title text-center mb-2">🎉 Super Sale Minggu Ini! 🎉</div>
  <p class="subtitle text-center mb-4">Promo gila-gilaan untuk koleksi kaos terbaik kami!</p>

  <div class="promo-grid">
    <?php
    /* Data promo — idealnya dari database */
    $promos = [
      ['foto/baju1.jpg', 'Kaos Hitam Polos',    '-40%',        '<s>Rp120.000</s> Rp72.000', 'Beli Sekarang'],
      ['foto/baju2.jpg', 'Kaos Coklat Vintage',  'FLASH SALE',  'Mulai Rp79.000',            'Lihat Detail'],
      ['foto/baju3.jpg', 'Kaos Oversize Cream',  'BUY 1 GET 1', 'Stok Terbatas!',            'Ambil Sekarang'],
      ['foto/baju4.jpg', 'Kaos Putih Casual',    '-30%',        '<s>Rp100.000</s> Rp70.000', 'Tambah ke Keranjang'],
      ['foto/baju5.jpg', 'Kaos Biru Navy',       'BARU!',       'Diskon Pembuka 25%',        'Beli Sekarang'],
      ['foto/baju6.jpg', 'Kaos Abu Modern',      'HOT DEAL🔥',  'Gratis Ongkir Hari Ini',   'Pesan Sekarang'],
      ['foto/baju1.jpg', 'Kaos Hitam Polos',    '-40%',        '<s>Rp120.000</s> Rp72.000', 'Beli Sekarang'],
      ['foto/baju2.jpg', 'Kaos Coklat Vintage',  'FLASH SALE',  'Mulai Rp79.000',            'Lihat Detail'],
    ];

    foreach ($promos as [$src, $name, $badge, $desc, $cta]) : ?>
      <div class="promo-card">
        <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($name) ?>" class="promo-img" loading="lazy">
        <div class="promo-overlay">
          <span class="badge-sale"><?= $badge ?></span>
          <h3><?= htmlspecialchars($name) ?></h3>
          <p><?= $desc /* sudah dikontrol, berisi HTML tag <s> */ ?></p>
          <button class="btn-promo" type="button"><?= htmlspecialchars($cta) ?></button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>


<!-- ========== KOLEKSI FAVORIT ========== -->
<section class="favorite reveal">
  <div class="container">
    <h2 class="text-center mb-3">✨ Koleksi Favorit Minggu Ini ✨</h2>

    <div class="favorite-grid">
      <?php
      $favorites = [
        ['foto/fav1.jpg', 'Kaos Pastel Oversize'],
        ['foto/fav2.jpg', 'Kaos Retro Line'],
        ['foto/fav3.jpg', 'Kaos Monokrom'],
      ];
      foreach ($favorites as [$src, $label]) : ?>
        <div class="fav-item">
          <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($label) ?>" loading="lazy">
          <p><?= htmlspecialchars($label) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ========== TESTIMONI ========== -->
<section class="testimoni reveal">
  <div class="container">
    <h2 class="text-center mb-2">Apa Kata Mereka 💬</h2>
    <p class="subtitle text-center mb-4">Ulasan jujur dari pelanggan setia kami</p>

    <div class="testimoni-grid">
      <?php
      $testimonials = [
        ['https://i.imgur.com/8Km9tLL.png', '"Kualitas bajunya lembut banget dan nyaman dipakai. Udah beli 3 kali!"', 'Rina, Bandung'],
        ['https://i.imgur.com/TIRbG8Y.png', '"Desainnya keren, cocok buat nongkrong. Pengiriman juga cepat!"',         'Arif, Jakarta'],
        ['https://i.imgur.com/jlEHZqv.png', '"Harga ramah di kantong tapi kualitas premium. Bakal order lagi!"',       'Laila, Surabaya'],
      ];
      foreach ($testimonials as [$avatar, $quote, $author]) : ?>
        <div class="testi-card">
          <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($author) ?>">
          <p><?= htmlspecialchars($quote) ?></p>
          <h4>— <?= htmlspecialchars($author) ?></h4>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>