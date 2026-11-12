<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    default: 'Skincare that <em>understands</em> you.'
  },
  lead: {
    type: String,
    default: 'A certified woman specialist comes to your home with genuine Nu Skin products and devices, listens to your skin concerns, and leaves you with a routine that fits your life.'
  },
  whatsappNumber: {
    type: String,
    default: '+917303706047'
  },
  whatsappMessage: {
    type: String,
    default: "Hi FMZ Glow, I'd like free advice about my skin."
  }
});

const waLink = computed(() => {
  const num = (props.whatsappNumber || '917303706047').replace(/[^0-9]/g, '');
  return `https://wa.me/${num}?text=${encodeURIComponent(props.whatsappMessage || "Hi FMZ Glow, I'd like free advice about my skin.")}`;
});
</script>

<template>
  <div>
    <section class="hero">
      <div class="wrap hero-grid">
        <div>
          <h1 v-html="title"></h1>
          <p class="lede">{{ lead }}</p>

          <div class="cities">
            <span class="pin">📍 Now serving</span> Ahmedabad, Gandhinagar, Surat, Rajkot
          </div>

          <div class="cta-row">
            <a class="btn" href="#book">Book your glow</a>
            <a class="btn ghost" :href="waLink" target="_blank" rel="noopener">Free skin advice on WhatsApp</a>
          </div>

          <div class="tags">
            <span>No travel charge within 8 km</span>
            <span>Pay after your visit</span>
          </div>
        </div>

        <figure class="hero-media">
          <img 
            src="/images/client-hero.jpg" 
            alt="FMZ Glow at-home skincare visit" 
            onerror="this.src='/images/fmz-glow-facial.png'"
          />
          <a href="#book" class="hero-badge">
            <span class="tag">LAUNCH OFFER</span>
            <b>FMZ Glow Facial</b>
            <span class="pr">₹999 <s>₹1,199</s></span>
            <small>Includes LumiSpa iO deep cleansing</small>
          </a>
        </figure>
      </div>
    </section>

    <!-- Trust Strip -->
    <section class="trust" aria-label="Why families trust FMZ Glow">
      <div class="wrap">
        <div class="trust-grid">
          <div>
            <b>Genuine Nu Skin</b>
            <span>Sealed, authentic products and devices on every visit</span>
          </div>
          <div>
            <b>Qualified women specialists</b>
            <span>Pharmacy, science and nursing backgrounds</span>
          </div>
          <div>
            <b>No hidden charges</b>
            <span>Free travel within 8 km; exact price confirmed first</span>
          </div>
          <div>
            <b>Pay after your visit</b>
            <span>UPI or cash in your hands once you’re satisfied</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
