<script setup>
import { ref, onMounted } from 'vue';

const isDark = ref(false);

const toggleTheme = () => {
  isDark.value = !isDark.value;
  const theme = isDark.value ? 'dark' : 'light';
  document.documentElement.dataset.theme = theme;
  try {
    localStorage.setItem('fmz-theme', theme);
  } catch (e) {}
};

onMounted(() => {
  try {
    const saved = localStorage.getItem('fmz-theme');
    if (saved === 'dark' || saved === 'light') {
      isDark.value = saved === 'dark';
      document.documentElement.dataset.theme = saved;
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      isDark.value = true;
      document.documentElement.dataset.theme = 'dark';
    }
  } catch (e) {}
});
</script>

<template>
  <header class="site-header">
    <div class="wrap nav">
      <a class="logo" href="#top" aria-label="FMZ Glow home">
        <span class="fmz">FMZ</span>
        <span class="glow">glow</span>
      </a>

      <ul>
        <li><a href="#services">Services</a></li>
        <li><a href="#how">How it works</a></li>
        <li><a href="#specialists">Specialists</a></li>
        <li><a href="#faq">FAQ</a></li>
      </ul>

      <div class="nav-right">
        <button 
          class="theme-btn" 
          type="button" 
          :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'" 
          :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
          @click="toggleTheme"
        >
          {{ isDark ? '☀️' : '🌙' }}
        </button>
        <a class="btn" href="#book">Book a visit</a>
      </div>
    </div>
  </header>
</template>
