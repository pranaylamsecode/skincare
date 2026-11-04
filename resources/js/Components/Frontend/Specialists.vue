<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  specialists: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['select-specialist']);

const activeFilter = ref('all');

const cities = [
  { id: 'all', label: 'All locations' },
  { id: 'ahmedabad', label: 'Ahmedabad' },
  { id: 'gandhinagar', label: 'Gandhinagar' },
  { id: 'surat', label: 'Surat' },
  { id: 'rajkot', label: 'Rajkot' }
];

const setFilter = (city) => {
  activeFilter.value = city;
};

const filteredSpecialists = computed(() => {
  if (activeFilter.value === 'all') {
    return props.specialists;
  }
  return props.specialists.filter(s => s.city.toLowerCase() === activeFilter.value.toLowerCase());
});

const bookWithSpecialist = (specialist) => {
  emit('select-specialist', specialist);
};
</script>

<template>
  <section class="specialists" id="specialists">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Our specialists</div>
        <h2>Meet our certified skin specialists</h2>
        <p>Qualified in science, biochemistry and nursing. Certified to deliver genuine Nu Skin treatments in the comfort of your home.</p>
        <div class="spec-tags">
          <span class="spec-tag">Women specialists only</span>
          <span class="spec-tag">Science &amp; formulation trained</span>
          <span class="spec-tag">Pay after service</span>
        </div>
      </div>

      <div class="spec-filter" role="group" aria-label="Filter specialists by city">
        <button 
          v-for="c in cities" 
          :key="c.id" 
          type="button" 
          :class="{ active: activeFilter === c.id }"
          @click="setFilter(c.id)"
        >
          {{ c.label }}
        </button>
      </div>

      <div class="spec-grid">
        <article 
          v-for="spec in filteredSpecialists" 
          :key="spec.id" 
          class="spec" 
          :data-city="spec.city"
        >
          <div class="spec-top">
            <img 
              class="spec-photo" 
              :src="spec.photo ? (spec.photo.startsWith('http') ? spec.photo : '/' + spec.photo) : '/images/Dharvi.jpg'" 
              :alt="spec.name"
            >
            <span class="spec-pill">&#10003; {{ spec.badge || 'Verified Specialist' }}</span>
          </div>
          <div class="spec-body">
            <h3>{{ spec.name }}</h3>
            <div class="spec-qual">{{ spec.qualification }}</div>
            <div class="spec-loc"><b>Location:</b> {{ spec.location }}</div>
            <div v-if="spec.languages && spec.languages.length" class="spec-langs">
              <span v-for="(lang, lIdx) in spec.languages" :key="lIdx">{{ lang }}</span>
            </div>
            <a 
              class="btn btn-line spec-book" 
              href="#book" 
              @click.prevent="bookWithSpecialist(spec)"
            >
              Book with {{ spec.name.split(' ')[0] }}
            </a>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>
