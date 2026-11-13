<script setup>
import { computed } from 'vue';

const props = defineProps({
  concerns: {
    type: Array,
    default: () => []
  },
  whatsappNumber: {
    type: String,
    default: '+917303706047'
  }
});

const emit = defineEmits(['select-concern']);

const cleanQuote = (text) => {
  if (!text) return '';
  return text.replace(/^[\s“”"'`]+|[\s“”"'`]+$/g, '').trim();
};

const defaultConcerns = [
  {
    key: 'facial',
    quote: 'My skin looks dull and tired',
    desc: 'The Glow Facial brings back a fresh, bright look.',
    cta: 'Book the Glow Facial',
    service: 'FMZ Glow Facial',
    is_dark: false
  },
  {
    key: 'cleanse',
    quote: 'I want a gentle first step',
    desc: 'Start with the Glow Cleanse: cleansing, hydration and advice.',
    cta: 'Book the Glow Cleanse',
    service: 'FMZ Glow Cleanse',
    is_dark: false
  },
  {
    key: 'ritual',
    quote: 'I deserve a proper pamper',
    desc: 'The Radiance Ritual is our most complete visit.',
    cta: 'Book the Radiance Ritual',
    service: 'FMZ Radiance Ritual',
    is_dark: false
  },
  {
    key: 'wa',
    quote: "I’m not sure what my skin needs",
    desc: 'Chat with us first. It’s free, with no obligation.',
    cta: 'Chat on WhatsApp',
    is_dark: true,
    is_whatsapp: true,
    wa_message: "Hi FMZ Glow, I'm not sure what my skin needs. Can you help?"
  }
];

const displayConcerns = computed(() => {
  if (props.concerns && props.concerns.length) {
    // Filter out extra/unwanted concerns
    const filtered = props.concerns.filter(c => {
      const q = ((c.quote || c.title) || '').toLowerCase();
      return !q.includes('event') && !q.includes('routine');
    });

    if (filtered.length >= 4) {
      return filtered.slice(0, 4).map((item, idx) => {
        const isDark = Boolean(item.is_dark) || idx === 3;
        const ref = defaultConcerns[idx] || defaultConcerns[3];
        const quoteText = cleanQuote(item.quote || item.title || ref.quote);
        const isWA = isDark || (item.link_target === 'whatsapp') || ((item.link_text || '').toLowerCase().includes('whatsapp'));

        return {
          quote: quoteText,
          desc: item.desc || item.solution || ref.desc,
          cta: item.cta || item.link_text || ref.cta,
          service: item.service || item.service_name || ref.service,
          is_dark: isDark,
          is_whatsapp: isWA,
          wa_message: ref.wa_message
        };
      });
    }
  }
  return defaultConcerns;
});

const handleCardClick = (item) => {
  if (item.is_whatsapp) {
    const rawNumber = props.whatsappNumber || '+917303706047';
    const cleanNumber = rawNumber.replace(/[^0-9]/g, '');
    const msg = encodeURIComponent(item.wa_message || "Hi FMZ Glow, I'm not sure what my skin needs. Can you help?");
    window.open(`https://wa.me/${cleanNumber}?text=${msg}`, '_blank');
  } else {
    emit('select-concern', { concern: item.quote, service: item.service });
    const bookEl = document.getElementById('book');
    if (bookEl) {
      bookEl.scrollIntoView({ behavior: 'smooth' });
    }
  }
};
</script>

<template>
  <section id="skin">
    <div class="wrap center">
      <h2>Tell us how your skin feels</h2>
      <p class="lede">Pick what sounds like you and we’ll suggest where to start.</p>
      
      <div class="concerns">
        <button 
          v-for="item in displayConcerns" 
          :key="item.quote"
          type="button"
          class="concern"
          :class="{ dark: item.is_dark }"
          @click="handleCardClick(item)"
        >
          <q>{{ item.quote }}</q>
          <span>{{ item.desc }}</span>
          <b>{{ item.cta }}</b>
        </button>
      </div>
    </div>
  </section>
</template>
