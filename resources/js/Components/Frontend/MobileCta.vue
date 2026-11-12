<script setup>
import { computed } from 'vue';

const props = defineProps({
  whatsappNumber: {
    type: String,
    default: '+917303706047'
  },
  whatsappMessage: {
    type: String,
    default: 'Hi FMZ Glow, I’d like to book a visit.'
  }
});

const waLink = computed(() => {
  const num = (props.whatsappNumber || '917303706047').replace(/[^0-9]/g, '');
  return `https://wa.me/${num}?text=${encodeURIComponent(props.whatsappMessage || 'Hi FMZ Glow, I’d like to book a visit.')}`;
});
</script>

<template>
  <div class="mbar">
    <a class="btn ghost" :href="waLink" target="_blank" rel="noopener">WhatsApp</a>
    <a class="btn" href="#book">Book a visit</a>
  </div>
</template>
