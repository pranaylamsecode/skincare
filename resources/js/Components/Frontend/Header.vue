<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  try {
    document.documentElement.dataset.theme = 'light';
    localStorage.removeItem('fmz-theme');
  } catch (e) {}
});
</script>

<template>
  <header class="site-header">
    <div class="wrap nav">
      <a class="logo" href="#top" aria-label="FMZ Glow home">
        <div class="logo-mark">
          <span class="fmz">FMZ</span>
          <span class="glow">glow</span>
        </div>
        <span class="logo-tagline">Skincare &amp; Wellness</span>
      </a>

      <ul>
        <li><a href="#services">Services</a></li>
        <li><a href="#how">How it works</a></li>
        <li><a href="#specialists">Specialists</a></li>
        <li><a href="#faq">FAQ</a></li>
      </ul>

      <div class="nav-right">
        <a class="btn" href="#book">Book a visit</a>
      </div>
    </div>
  </header>
</template>
