<script setup>
import { computed } from 'vue';

const props = defineProps({
  services: {
    type: Array,
    default: () => []
  },
  plans: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['select-service']);

const selectService = (serviceTitle) => {
  emit('select-service', serviceTitle);
  const bookEl = document.getElementById('book');
  if (bookEl) {
    bookEl.scrollIntoView({ behavior: 'smooth' });
  }
};

const formatPrice = (price) => {
  if (!price && price !== 0) return '';
  return '₹' + Math.round(Number(price)).toLocaleString('en-IN');
};

const getDuration = (svc) => {
  if (svc && svc.duration_minutes) return svc.duration_minutes;
  const t = ((svc && svc.title) || '').toLowerCase();
  if (t.includes('cleanse')) return 45;
  if (t.includes('ritual')) return 75;
  return 60;
};

const getTag = (svc) => {
  if (svc && svc.is_featured) return 'Launch offer';
  if (svc && svc.tag) {
    const l = svc.tag.toLowerCase();
    if (l.includes('gentle')) return 'A gentle start';
    if (l.includes('complete') || l.includes('premium')) return 'Most complete';
    return svc.tag;
  }
  const t = ((svc && svc.title) || '').toLowerCase();
  if (t.includes('cleanse')) return 'A gentle start';
  if (t.includes('ritual')) return 'Most complete';
  return 'At-home care';
};

const getFeatures = (svc) => {
  if (svc && svc.features) {
    if (Array.isArray(svc.features) && svc.features.length) return svc.features;
    if (typeof svc.features === 'string') {
      try {
        const parsed = JSON.parse(svc.features);
        if (Array.isArray(parsed) && parsed.length) return parsed;
      } catch (e) {
        const lines = svc.features.split('\n').map(s => s.trim()).filter(Boolean);
        if (lines.length) return lines;
      }
    }
  }
  const t = ((svc && svc.title) || '').toLowerCase();
  if (t.includes('cleanse')) {
    return [
      'Skin suitability check',
      'LumiSpa iO cleanse',
      'Moisturising finish',
      'Simple aftercare tips'
    ];
  } else if (t.includes('ritual')) {
    return [
      'Extended consultation',
      'Galvanic Spa facial',
      'Prysm iO antioxidant score',
      'Personal skincare and food tips'
    ];
  } else {
    return [
      'Skin consultation',
      'Galvanic Spa facial treatment',
      'Leaves skin soft and refreshed',
      'Home-care tips'
    ];
  }
};

const getButtonLabel = (title) => {
  if (!title) return 'Book now';
  const clean = title.replace(/^FMZ\s+/i, '').replace('Nu Skin LumiSpa ', '').trim();
  return `Book ${clean}`;
};

const activePlan = computed(() => {
  if (props.plans && props.plans.length) {
    const p = props.plans.find(x => (x.title || x.name || '').toLowerCase().includes('3-visit')) || props.plans[0];
    if (p) {
      const rawPrice = Number(p.price);
      const price = rawPrice === 3299 || !rawPrice ? 2799 : rawPrice;
      const perVisit = Math.round(price / 3);
      const desc = (p.description && !p.description.includes('3299') && !p.description.includes('298'))
        ? p.description
        : 'Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.';
      return {
        title: p.title || p.name || 'Glow 3-Visit Plan',
        price: price,
        subtext: `₹${perVisit} per visit`,
        description: desc
      };
    }
  }
  return {
    title: 'Glow 3-Visit Plan',
    price: 2799,
    subtext: '₹933 per visit',
    description: 'Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.'
  };
});
</script>

<template>
  <section class="services" id="services">
    <div class="wrap">
      <div class="center">
        <h2>Choose your visit</h2>
        <p class="lede">Every visit starts with a short skin check. Your specialist explains each step before she begins.</p>
      </div>

      <div class="svc-grid">
        <template v-if="services && services.length">
          <article 
            v-for="svc in services" 
            :key="svc.id" 
            class="svc"
            :class="{ feature: svc.is_featured }"
          >
            <span class="tag">{{ getTag(svc) }}</span>
            <h3>{{ svc.title }}</h3>
            <div class="meta">
              <span>⏱ About {{ getDuration(svc) }} min</span>
            </div>
            <p v-if="svc.description">{{ svc.description }}</p>
            <ul>
              <li v-for="(feat, idx) in getFeatures(svc)" :key="idx">{{ feat }}</li>
            </ul>
            <div class="price">
              <b>{{ formatPrice(svc.price) }}</b>
              <s v-if="svc.original_price">{{ formatPrice(svc.original_price) }}</s>
            </div>
            <button 
              type="button" 
              class="btn" 
              :class="svc.is_featured ? '' : 'ghost'"
              @click="selectService(svc.title)"
            >
              {{ getButtonLabel(svc.title) }}
            </button>
          </article>
        </template>

        <template v-else>
          <article class="svc">
            <span class="tag">A gentle start</span>
            <h3>FMZ Glow Cleanse</h3>
            <div class="meta"><span>⏱ About 45 min</span></div>
            <p>Deep cleansing and hydration with the Nu Skin LumiSpa iO.</p>
            <ul>
              <li>Skin suitability check</li>
              <li>LumiSpa iO cleanse</li>
              <li>Moisturising finish</li>
              <li>Simple aftercare tips</li>
            </ul>
            <div class="price"><b>₹799</b></div>
            <button type="button" class="btn ghost" @click="selectService('FMZ Glow Cleanse')">Book Glow Cleanse</button>
          </article>

          <article class="svc feature">
            <span class="tag">Launch offer</span>
            <h3>FMZ Glow Facial</h3>
            <div class="meta"><span>⏱ About 60 min</span></div>
            <p>Our signature facial with the Nu Skin Galvanic Spa and selected Nu Skin products.</p>
            <ul>
              <li>Skin consultation</li>
              <li>Galvanic Spa facial treatment</li>
              <li>Leaves skin soft and refreshed</li>
              <li>Home-care tips</li>
            </ul>
            <div class="price"><b>₹999</b><s>₹1,199</s></div>
            <button type="button" class="btn" @click="selectService('FMZ Glow Facial')">Book Glow Facial</button>
          </article>

          <article class="svc">
            <span class="tag">Most complete</span>
            <h3>FMZ Radiance Ritual</h3>
            <div class="meta"><span>⏱ About 75 min</span></div>
            <p>The Glow Facial plus a 15-second Prysm iO antioxidant scan, explained by your specialist.</p>
            <ul>
              <li>Extended consultation</li>
              <li>Galvanic Spa facial</li>
              <li>Prysm iO antioxidant score</li>
              <li>Personal skincare and food tips</li>
            </ul>
            <div class="price"><b>₹1,699</b></div>
            <button type="button" class="btn ghost" @click="selectService('FMZ Radiance Ritual')">Book Radiance Ritual</button>
          </article>
        </template>
      </div>

      <!-- Plan Card -->
      <div class="plan">
        <div>
          <h3>{{ activePlan.title }}: {{ formatPrice(activePlan.price) }} <span class="small">({{ activePlan.subtext }})</span></h3>
          <p>{{ activePlan.description }}</p>
        </div>
        <button type="button" class="btn ghost" @click="selectService(activePlan.title)">Book the plan</button>
      </div>
    </div>
  </section>
</template>
