<script setup>
defineProps({
  whatsappNumber: {
    type: String,
    default: '+917303706047'
  },
  whatsappMessage: {
    type: String,
    default: "Hi FMZ Glow, I'd like to know more about your at-home skincare services."
  }
});
</script>

<template>
  <section class="expert" id="expert">
    <div class="expert-grid">
      <div>
        <div class="eyebrow">Expert guidance</div>
        <h2>Not sure where to start? Just ask us.</h2>
        <p>Tell us about your skin on WhatsApp. A trained FMZ Glow expert will suggest the right service or routine for you. Free, private and no pressure to book.</p>
      </div>
      <div class="expert-cta">
        <a 
          class="btn btn-tan" 
          :href="`https://wa.me/${whatsappNumber.replace(/[^0-9+]/g, '')}?text=${encodeURIComponent(whatsappMessage)}`" 
          target="_blank" 
          rel="noopener"
        >
          Chat on WhatsApp
        </a>
      </div>
    </div>
  </section>
</template>
