<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isMenuOpen = ref(false);

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const closeMenu = () => {
  isMenuOpen.value = false;
};

const handleResize = () => {
  if (window.innerWidth > 820) {
    isMenuOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});
</script>

<template>
  <header class="site-header">
    <div class="wrap nav">
      <a class="logo" href="#" aria-label="FMZ Glow home">
        <img src="/images/FMZ logo.jpg" alt="FMZ Glow" width="143" height="46">
      </a>
      <nav class="nav-links" aria-label="Main navigation">
        <a href="#skin">Your skin</a>
        <a href="#services">Services</a>
        <a href="#plans">Plans</a>
        <a href="#products">Products</a>
        <a href="#how">How it works</a>
        <a href="#faq">FAQ</a>
        <a class="btn" href="#book">Book a visit</a>
      </nav>
      <button 
        class="menu-btn" 
        type="button" 
        :aria-expanded="isMenuOpen" 
        aria-label="Toggle menu" 
        @click="toggleMenu"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
    <nav class="mobile-menu" :class="{ open: isMenuOpen }" aria-label="Mobile navigation">
      <a href="#skin" @click="closeMenu">Your skin</a>
      <a href="#services" @click="closeMenu">Services</a>
      <a href="#plans" @click="closeMenu">Plans</a>
      <a href="#products" @click="closeMenu">Products</a>
      <a href="#how" @click="closeMenu">How it works</a>
      <a href="#faq" @click="closeMenu">FAQ</a>
      <a href="#book" @click="closeMenu">Book a visit</a>
    </nav>
  </header>
</template>
