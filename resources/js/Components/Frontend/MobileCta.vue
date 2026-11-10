<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isHidden = ref(false);
let observer = null;

const scrollToBook = () => {
  const el = document.getElementById('book');
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' });
  } else {
    window.location.hash = '#book';
  }
};

onMounted(() => {
  const bookSection = document.getElementById('book');
  if (bookSection && 'IntersectionObserver' in window) {
    observer = new IntersectionObserver((entries) => {
      isHidden.value = entries[0].isIntersecting;
    }, { threshold: 0.15 });
    observer.observe(bookSection);
  }
});

onUnmounted(() => {
  if (observer) {
    observer.disconnect();
  }
});
</script>

<template>
  <div class="mobile-cta" :class="{ hide: isHidden }">
    <a class="btn" href="#book" @click="scrollToBook">Book your glow</a>
  </div>
</template>
