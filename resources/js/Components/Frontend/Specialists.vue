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

        <!-- Become a CME Callout -->
        <div class="cme-callout">
          <div class="cme-text">
            <span class="cme-tag">Join as a Specialist</span>
            <h4>Qualified in science, pharmacy, or nursing?</h4>
            <p>Become an FMZ Certified Micro Entrepreneur (CME). We provide training, Nu Skin equipment support, and direct customer bookings.</p>
          </div>
          <a href="/become-a-cme" class="cme-action">
            <span>Apply as CME · Free</span>
            <span class="arrow">→</span>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.cme-callout {
  margin-top: 36px;
  background: #FFFFFF;
  border: 1px solid var(--line);
  border-radius: 20px;
  padding: 24px 30px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
}

.cme-text {
  flex: 1;
}

.cme-tag {
  display: inline-block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--wine);
  margin-bottom: 6px;
}

.cme-text h4 {
  font-family: 'Cormorant Garamond', Georgia, serif;
  font-size: 24px;
  font-weight: 600;
  color: var(--ink);
  margin: 0 0 4px;
}

.cme-text p {
  font-size: 13.5px;
  color: var(--muted);
  margin: 0;
  line-height: 1.5;
}

.cme-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 999px;
  background: var(--wine);
  color: #FFFFFF;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 600;
  white-space: nowrap;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(122, 42, 46, 0.2);
}

.cme-action:hover {
  background: #5a1f27;
  transform: translateY(-1px);
}

.cme-action .arrow {
  transition: transform 0.2s;
}

.cme-action:hover .arrow {
  transform: translateX(3px);
}

@media (max-width: 768px) {
  .cme-callout {
    flex-direction: column;
    align-items: flex-start;
    padding: 20px;
  }
  .cme-action {
    width: 100%;
    justify-content: center;
  }
}
</style>
