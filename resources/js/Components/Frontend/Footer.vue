<script setup>
const currentYear = new Date().getFullYear();
</script>

<template>
  <footer>
    <div class="wrap foot">
      <img src="/images/FMZ logo.jpg" alt="FMZ Glow" width="118" height="38">
      <nav aria-label="Footer">
        <a href="#services">Services</a>
        <a href="#plans">Plans</a>
        <a href="#faq">FAQ</a>
        <a href="/privacy-policy/">Privacy Policy</a>
        <a href="/terms-and-conditions/">Terms</a>
        <a href="/admin/login" style="opacity:0.6">Admin Portal</a>
      </nav>
      <p>© {{ currentYear }} FMZ India. All rights reserved.</p>
    </div>
  </footer>
</template>
