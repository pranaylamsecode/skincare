<script setup>
defineProps({
  eyebrow: {
    type: String,
    default: 'At-home skincare by FMZ India'
  },
  title: {
    type: String,
    default: 'Skincare that<br><em>understands</em> you.'
  },
  lead: {
    type: String,
    default: 'We listen to your skin concerns first. Then a certified woman specialist brings professional Nu Skin care to your home, explains every step, and leaves you with a routine that fits your life.'
  },
  tagline: {
    type: String,
    default: 'Your Glow. Your Home. Your Time.'
  }
});
</script>

<template>
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <div class="eyebrow">{{ eyebrow }}</div>
        <h1 v-html="title"></h1>
        <p class="lead">{{ lead }}</p>
        <p class="tagline">{{ tagline }}</p>
        <div class="actions">
          <a class="btn" href="#book">Book your glow</a>
          <a class="btn btn-line" href="#expert">Get free skin guidance</a>
        </div>
        <div class="chips">
          <span class="chip">Women specialists only</span>
          <span class="chip">Genuine Nu Skin products</span>
          <span class="chip">Clear prices, no pressure</span>
          <span class="chip">Pay after your visit</span>
        </div>
      </div>
      <div class="ph hero-photo">
        <img 
          src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?auto=format&fit=crop&w=1000&q=85" 
          alt="Woman relaxing at home during a facial" 
          loading="lazy" 
          onerror="this.remove()"
        >
        <span>Photo direction: an Indian woman relaxing at home with eyes closed, a specialist’s hands gently applying care. Soft natural light, warm tones, real skin, no heavy retouching.</span>
      </div>
    </div>
  </section>
</template>
