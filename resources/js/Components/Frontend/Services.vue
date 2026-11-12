<script setup>
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
  if (!price) return '';
  return '₹' + Math.round(Number(price)).toLocaleString('en-IN');
};
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
            <span class="tag">{{ svc.tag || (svc.is_featured ? 'Launch offer' : 'At-home care') }}</span>
            <h3>{{ svc.title }}</h3>
            <div class="meta" v-if="svc.duration_minutes">
              <span>⏱ About {{ svc.duration_minutes }} min</span>
            </div>
            <p v-if="svc.description">{{ svc.description }}</p>
            <ul v-if="svc.features && svc.features.length">
              <li v-for="(feat, idx) in svc.features" :key="idx">{{ feat }}</li>
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
              Book {{ svc.title.replace('Nu Skin LumiSpa ', '') }}
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
        <template v-if="plans && plans.length">
          <div>
            <h3>{{ plans[0].name }}: {{ formatPrice(plans[0].price) }} <span class="small">(₹{{ Math.round(Number(plans[0].price) / 3) }} per visit)</span></h3>
            <p>{{ plans[0].description || 'Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.' }}</p>
          </div>
          <button type="button" class="btn ghost" @click="selectService(plans[0].name)">Book the plan</button>
        </template>
        <template v-else>
          <div>
            <h3>Glow 3-Visit Plan: ₹2,799 <span class="small">(₹933 per visit)</span></h3>
            <p>Three Glow Facials with the same specialist, spaced to suit your skin. Valid 90 days.</p>
          </div>
          <button type="button" class="btn ghost" @click="selectService('Glow 3-Visit Plan')">Book the plan</button>
        </template>
      </div>
    </div>
  </section>
</template>
