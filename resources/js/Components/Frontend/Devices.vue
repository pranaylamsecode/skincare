<script setup>
defineProps({
  devices: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <section class="devices" id="devices">
    <div class="wrap">
      <div class="head">
        <h2>Meet the devices behind your glow</h2>
        <p>Next-level skincare and wellness solutions</p>
      </div>
      <div class="dev-grid">
        <article 
          v-for="(dev, idx) in devices" 
          :key="dev.id" 
          class="dev" 
          :class="{ mid: idx === 1 }"
        >
          <div class="ph">
            {{ dev.name }}
            <img 
              v-if="dev.image" 
              :src="dev.image.startsWith('http') ? dev.image : '/' + dev.image" 
              :alt="dev.name" 
              loading="lazy" 
              onerror="this.remove()"
            >
            <span class="dev-pill">{{ dev.pill || 'Premium' }}</span>
          </div>
          <div class="dev-body">
            <h3>{{ dev.name }}</h3>
            <p>{{ dev.description }}</p>
            <ul v-if="dev.features && dev.features.length">
              <li v-for="(feat, fIdx) in dev.features" :key="fIdx">{{ feat }}</li>
            </ul>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>
