<script setup>
defineProps({
  dailyProducts: {
    type: Array,
    default: () => []
  },
  wellnessProducts: {
    type: Array,
    default: () => []
  }
});

const formatImgUrl = (img) => {
  if (!img) return '';
  if (img.startsWith('http://') || img.startsWith('https://')) {
    return img;
  }
  return img.startsWith('/') ? img : '/' + img;
};
</script>

<template>
  <section class="products" id="products">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Continue your glow at home</div>
        <h2>Products chosen for your skin</h2>
        <p>Your specialist recommends a simple routine from the Nu Skin range and shows you how to use it. Order through your specialist and get her guidance with every product.</p>
      </div>

      <p class="sub">Daily skincare</p>
      <div class="daily">
        <div v-for="prod in dailyProducts" :key="prod.id" class="prod">
          <div class="ph">
            <template v-if="!prod.image">Product photo</template>
            <img 
              v-if="prod.image" 
              :key="prod.id + '_' + prod.image"
              :src="formatImgUrl(prod.image)" 
              :alt="prod.name" 
              loading="lazy" 
              @error="(e) => e.target.style.display = 'none'"
              @load="(e) => e.target.style.display = 'block'"
            >
          </div>
          <small v-if="prod.step_name">{{ prod.step_name }}</small>
          <b>{{ prod.name }}</b>
        </div>
      </div>

      <p class="sub">Wellness from within</p>
      <div class="wellness">
        <div v-for="well in wellnessProducts" :key="well.id" class="well">
          <div v-if="well.image" class="ph well-ph">
            <img 
              :src="formatImgUrl(well.image)" 
              :alt="well.name" 
              loading="lazy" 
              onerror="this.style.display='none'"
            >
          </div>
          <b>{{ well.name }}</b>
          <p>{{ well.description }}</p>
        </div>
      </div>

      <div class="notice">
        <p>Supplements are not a substitute for a varied diet and are not meant to diagnose, treat or prevent any disease. Speak to your doctor before starting any supplement.</p>
        <a class="btn btn-maroon" href="#expert">Ask about products</a>
      </div>
    </div>
  </section>
</template>
