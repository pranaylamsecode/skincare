<script setup>
const props = defineProps({
  concerns: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['select-concern']);

const cleanQuote = (text) => {
  if (!text) return '';
  return text.replace(/^[“"']+|[”"']+$/g, '').trim();
};

const defaultConcerns = [
  {
    title: 'Dull, tired skin',
    desc: 'Lacks glow, feels drained from work and travel',
    cta: 'Cleanse & revive →',
    service: 'FMZ Glow Cleanse',
    is_dark: false
  },
  {
    title: 'Rough texture & pores',
    desc: 'Uneven to touch, buildup around nose and chin',
    cta: 'Deep pores & glow →',
    service: 'FMZ Glow Facial',
    is_dark: true
  },
  {
    title: 'Fine lines & losing firmness',
    desc: 'Feels less elastic, needs gentle sculpting',
    cta: 'Firm & sculpt →',
    service: 'FMZ Radiance Ritual',
    is_dark: false
  },
  {
    title: 'Just want a healthy reset',
    desc: 'Start fresh and learn what my skin actually needs',
    cta: 'Reset & routine →',
    service: 'Glow 3-Visit Plan',
    is_dark: false
  }
];

const pickConcern = (concern, service) => {
  emit('select-concern', { concern, service });
  const bookEl = document.getElementById('book');
  if (bookEl) {
    bookEl.scrollIntoView({ behavior: 'smooth' });
  }
};
</script>

<template>
  <section id="skin">
    <div class="wrap center">
      <h2>Tell us how your skin feels</h2>
      <p class="lede">Pick what sounds most like you. Your specialist customises your visit around it.</p>
      
      <div class="concerns">
        <template v-if="concerns && concerns.length">
          <button 
            v-for="(item, index) in concerns" 
            :key="item.id || index"
            type="button"
            class="concern"
            :class="{ dark: Boolean(item.is_dark) }"
            @click="pickConcern(item.title || item.quote, item.service_name || defaultConcerns[index % 4].service)"
          >
            <q>{{ cleanQuote(item.quote || item.title) }}</q>
            <span>{{ item.desc || item.solution || defaultConcerns[index % 4].desc }}</span>
            <b>{{ item.cta || item.link_text || defaultConcerns[index % 4].cta }}</b>
          </button>
        </template>

        <template v-else>
          <button 
            v-for="(c, i) in defaultConcerns"
            :key="i"
            type="button"
            class="concern"
            :class="{ dark: c.is_dark }"
            @click="pickConcern(c.title, c.service)"
          >
            <q>{{ cleanQuote(c.title) }}</q>
            <span>{{ c.desc }}</span>
            <b>{{ c.cta }}</b>
          </button>
        </template>
      </div>
    </div>
  </section>
</template>
