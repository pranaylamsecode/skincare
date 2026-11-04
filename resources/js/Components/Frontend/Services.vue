<script setup>
const props = defineProps({
  services: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['select-service']);

const selectService = (serviceTitle) => {
  emit('select-service', serviceTitle);
};

const formatPrice = (price) => {
  return '₹' + Math.round(Number(price)).toLocaleString('en-IN');
};
</script>

<template>
  <section class="services" id="services">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Our services</div>
        <h2>Choose your experience</h2>
        <p>Every visit starts with a conversation about your skin. Your specialist checks what suits you before she begins, and explains each step.</p>
      </div>

      <div class="svc-grid">
        <article 
          v-for="svc in services" 
          :key="svc.id" 
          class="svc" 
          :class="{ feat: svc.is_featured }"
        >
          <div class="ph">
            <template v-if="!svc.image">
              Photo: gentle care at home
            </template>
            <img 
              v-if="svc.image" 
              :src="svc.image.startsWith('http') ? svc.image : '/' + svc.image" 
              :alt="svc.title" 
              loading="lazy" 
              onerror="this.remove()"
            >
          </div>

          <div class="svc-body">
            <span 
              v-if="svc.tag" 
              class="tag" 
              :class="{
                tan: svc.tag_style === 'tan',
                dark: svc.tag_style === 'dark'
              }"
            >
              {{ svc.tag }}
            </span>

            <h3>{{ svc.title }}</h3>
            <p v-if="svc.description">{{ svc.description }}</p>

            <ul v-if="svc.features && svc.features.length">
              <li v-for="(feat, idx) in svc.features" :key="idx">{{ feat }}</li>
            </ul>

            <div class="price">
              {{ formatPrice(svc.price) }}
              <s v-if="svc.original_price">{{ formatPrice(svc.original_price) }}</s>
              <small v-if="svc.launch_label">{{ svc.launch_label }}</small>
            </div>

            <a 
              class="btn pkg" 
              :class="svc.is_featured ? '' : 'btn-line'" 
              href="#book" 
              @click.prevent="selectService(svc.title)"
            >
              Book this service
            </a>
          </div>
        </article>
      </div>

      <!-- Coming soon -->
      <div class="coming">
        <div class="coming-top">
          <h3>Coming soon</h3>
          <span>Opening as our specialists complete training for each one</span>
        </div>
        <div class="coming-grid">
          <div>
            <b>Glow Facial + Prysm iO Wellness Insight</b>
            <span>Your Glow Facial plus a skin carotenoid reading, explained</span>
          </div>
          <div>
            <b>Basic cleanup</b>
            <span>A quick refresh between facials</span>
          </div>
          <div>
            <b>Hydration-focused facial</b>
            <span>For skin that feels dry or tight</span>
          </div>
          <div>
            <b>Event-ready skincare</b>
            <span>Prepare your skin before a special day</span>
          </div>
          <div>
            <b>Hand and foot care add-on</b>
            <span>Add to any facial visit</span>
          </div>
        </div>
        <a class="link" href="#expert">Tell me when these launch →</a>
      </div>
    </div>
  </section>
</template>
