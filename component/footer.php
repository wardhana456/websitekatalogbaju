<!-- ========== FOOTER ========== -->
<footer class="footer">
  <div class="footer-top">

    <div class="footer-section">
      <h4>Jelajahi</h4>
      <ul>
        <li><a href="#">Tentang Kami</a></li>
        <li><a href="#">Blog Fashion</a></li>
        <li><a href="#">Cara Belanja</a></li>
        <li><a href="#">FAQ</a></li>
      </ul>
    </div>

    <div class="footer-section">
      <h4>Ikuti Kami</h4>
      <ul class="social-icons">
        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
        <li><a href="#"><i class="fab fa-facebook-f"></i> Facebook</a></li>
        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
        <li><a href="#"><i class="fab fa-tiktok"></i> TikTok</a></li>
      </ul>
    </div>

  </div>

  <hr>

  <div class="footer-bottom">
    <div class="footer-links">
      <a href="#">Kebijakan Cookie</a>
      <a href="#">Kebijakan Privasi</a>
      <a href="#">Syarat & Ketentuan</a>
    </div>
    <p class="footer-copy">© <?= date('Y') ?> ThriftPay. All rights reserved.</p>
  </div>
</footer>

<!-- Bootstrap JS Bundle (termasuk Popper) -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jyor+4dd22FCMbFVCrCxMaAxHLqh5"
  crossorigin="anonymous"
></script>

<!-- ========== Scroll Reveal (micro-interaction ringan) ========== -->
<script>
  /* Reveal elemen dengan class .reveal saat masuk viewport */
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target); /* fire sekali saja */
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

</body>
</html>