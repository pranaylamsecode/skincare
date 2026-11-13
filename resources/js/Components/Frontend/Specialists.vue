<script setup>
import { ref } from 'vue';

const props = defineProps({
  specialists: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['select-specialist']);

const scrollContainer = ref(null);

const scrollLeft = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: -300, behavior: 'smooth' });
  }
};

const scrollRight = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: 300, behavior: 'smooth' });
  }
};

const getInitials = (name) => {
  if (!name) return 'FM';
  const parts = name.trim().split(/\s+/);
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return parts[0].substring(0, 2).toUpperCase();
};

const getFirstName = (name) => {
  if (!name) return '';
  return name.trim().split(/\s+/)[0];
};

const bookWithSpecialist = (spec) => {
  emit('select-specialist', spec);
  const bookEl = document.getElementById('book');
  if (bookEl) {
    bookEl.scrollIntoView({ behavior: 'smooth' });
  }
};
</script>

<template>
  <section class="specialists" id="specialists">
    <div class="wrap">
      <div class="center">
        <h2>Meet your specialists</h2>
        <p class="lede">Qualified in science, pharmacy or nursing, and trained to deliver genuine Nu Skin care at home.</p>
      </div>

      <!-- Side-Scroll Specialist Track -->
      <div class="sp-scroll-wrap">
        <!-- Scroll navigation arrow buttons for desktop -->
        <div class="flex items-center justify-end gap-2 mb-3">
          <button 
            type="button" 
            class="w-9 h-9 rounded-full border border-[#EFDCD8] bg-white text-[#7A2A2E] hover:bg-[#F9E9E6] transition flex items-center justify-center font-bold text-base cursor-pointer shadow-xs"
            aria-label="Scroll left"
            @click="scrollLeft"
          >
            ←
          </button>
          <button 
            type="button" 
            class="w-9 h-9 rounded-full border border-[#EFDCD8] bg-white text-[#7A2A2E] hover:bg-[#F9E9E6] transition flex items-center justify-center font-bold text-base cursor-pointer shadow-xs"
            aria-label="Scroll right"
            @click="scrollRight"
          >
            →
          </button>
        </div>

        <div class="sp-scroll-track" ref="scrollContainer">
          <article 
            v-for="spec in specialists" 
            :key="spec.id" 
            class="sp"
          >
            <div class="avatar">
              <img 
                v-if="spec.photo" 
                :src="spec.photo.startsWith('http') ? spec.photo : '/' + spec.photo" 
                :alt="getFirstName(spec.name)"
                onerror="this.style.display='none'"
              />
              <span v-else>{{ getInitials(spec.name) }}</span>
            </div>

            <span class="verified">✓ {{ spec.badge || 'Verified specialist' }}</span>
            <h3>{{ getFirstName(spec.name) }}</h3>
            <span class="cred">{{ spec.qualification }}</span>
            <span class="area" v-if="spec.languages && spec.languages.length">
              {{ Array.isArray(spec.languages) ? spec.languages.join(', ') : spec.languages }}
            </span>

            <button 
              type="button" 
              class="btn ghost" 
              @click="bookWithSpecialist(spec)"
            >
              Book Appointment
            </button>
          </article>
        </div>
      </div>
    </div>
  </section>
</template>
