/* =============================================
   THRIFTPAY — main.js
   Refactored: scroll-reveal, back-to-top, filters
   ============================================= */

document.addEventListener('DOMContentLoaded', () => {

  /* ── 1. SCROLL REVEAL ─────────────────────
     Tambahkan class .reveal atau .reveal-stagger
     ke elemen di HTML untuk animasi masuk
  ────────────────────────────────────────── */
  const revealEls = document.querySelectorAll('.reveal, .reveal-stagger');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); // hanya animasi sekali
      }
    });
  }, {
    threshold: 0.12,
    rootMargin: '0px 0px -40px 0px'
  });

  revealEls.forEach(el => observer.observe(el));


  /* ── 2. BACK TO TOP BUTTON ────────────────
     Otomatis muncul setelah scroll 300px
  ────────────────────────────────────────── */
  const btnTop = document.getElementById('backToTop');

  if (btnTop) {
    window.addEventListener('scroll', () => {
      btnTop.classList.toggle('show', window.scrollY > 300);
    }, { passive: true });

    btnTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }


  /* ── 3. FILTER BUTTON ACTIVE STATE ───────
     Untuk halaman shop — klik button filter
  ────────────────────────────────────────── */
  const filterBtns = document.querySelectorAll('.filters button');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });


  /* ── 4. SMOOTH NAVBAR SHADOW ON SCROLL ───
     Navbar lebih solid saat halaman di-scroll
  ────────────────────────────────────────── */
  const navbar = document.querySelector('.navbar-gradient');

  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.style.boxShadow = window.scrollY > 20
        ? '0 4px 24px rgba(0,0,0,.25)'
        : '0 4px 16px rgba(0,0,0,.10)';
    }, { passive: true });
  }


  /* ── 5. ADD TO CART MICRO-INTERACTION ────
     Animasi kecil saat tombol diklik
  ────────────────────────────────────────── */
  document.querySelectorAll('.product-card button').forEach(btn => {
    btn.addEventListener('click', function () {
      const original = this.textContent;
      this.textContent = '✓ Ditambahkan!';
      this.style.background = '#10b981'; // hijau sukses
      setTimeout(() => {
        this.textContent = original;
        this.style.background = '';
      }, 1400);
    });
  });

});