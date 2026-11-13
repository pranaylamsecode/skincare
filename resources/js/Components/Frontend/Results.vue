<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  results: {
    type: Array,
    default: () => []
  },
  reviews: {
    type: Array,
    default: () => []
  }
});

const defaultResults = [
  {
    client_name: 'Priya, 28',
    plan_info: 'FMZ Glow Facial, 3 visits over 12 weeks',
    concern: 'Dull, tired skin',
    quote: '“My skin looks fresher and my makeup sits better.”',
    image_before: '/images/priya-before.jpg',
    image_after: '/images/priya-after.jpg'
  },
  {
    client_name: 'Neha, 34',
    plan_info: 'FMZ Glow Cleanse, 2 visits over 4 weeks',
    concern: 'Dry, tight skin',
    quote: '“It feels soft and comfortable all day.”',
    image_before: '/images/neha-before.jpg',
    image_after: '/images/neha-after.jpg'
  }
];

const defaultReviews = [
  {
    quote: '“The facial was so relaxing and my skin felt soft and refreshed. The specialist made me feel comfortable throughout.”',
    client_name: 'Mansi Joshi, Ahmedabad'
  },
  {
    quote: '“The antioxidant scan was new for me. I understand my skin and diet much better now.”',
    client_name: 'Hetal Patel, Gandhinagar'
  },
  {
    quote: '“Having a certified woman specialist visit my home was wonderful and convenient. Truly authentic Nu Skin care.”',
    client_name: 'Pooja Shah, Surat'
  }
];

const getImages = (res, idx) => {
  let before = res.image_before;
  let after = res.image_after;

  if (!before && idx === 0) before = '/images/priya-before.jpg';
  if (!after && idx === 0) after = '/images/priya-after.jpg';
  if (!before && idx === 1) before = '/images/neha-before.jpg';
  if (!after && idx === 1) after = '/images/neha-after.jpg';

  if (before && before.endsWith('priya.jpg')) before = '/images/priya-before.jpg';
  if (after && after.endsWith('priya.jpg')) after = '/images/priya-after.jpg';
  if (before && before.endsWith('neha.jpg')) before = '/images/neha-before.jpg';
  if (after && after.endsWith('neha.jpg')) after = '/images/neha-after.jpg';

  if (before && !before.startsWith('/') && !before.startsWith('http')) before = '/' + before;
  if (after && !after.startsWith('/') && !after.startsWith('http')) after = '/' + after;

  return {
    before: before || '/images/priya-before.jpg',
    after: after || '/images/priya-after.jpg'
  };
};

// Interactive Before/After slider state
const sliderPositions = ref([50, 50, 50, 50]);
const isDragging = ref([false, false, false, false]);
const hasInteracted = ref([false, false, false, false]);
const sliderRefs = ref([]);
const resultsSectionRef = ref(null);

const setSliderRef = (el, idx) => {
  if (el) sliderRefs.value[idx] = el;
};

const updateSliderFromEvent = (e, idx) => {
  const container = sliderRefs.value[idx];
  if (!container) return;
  const rect = container.getBoundingClientRect();
  const clientX = e.clientX ?? (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
  const offsetX = clientX - rect.left;
  const percentage = Math.max(0, Math.min(100, (offsetX / rect.width) * 100));
  sliderPositions.value[idx] = Math.round(percentage);
};

const startDrag = (e, idx) => {
  hasInteracted.value[idx] = true;
  isDragging.value[idx] = true;
  if (e.target && e.target.setPointerCapture) {
    try {
      e.target.setPointerCapture(e.pointerId);
    } catch (_) {}
  }
  updateSliderFromEvent(e, idx);
};

const onDrag = (e, idx) => {
  if (!isDragging.value[idx]) return;
  updateSliderFromEvent(e, idx);
};

const stopDrag = (e, idx) => {
  isDragging.value[idx] = false;
  if (e.target && e.target.releasePointerCapture) {
    try {
      e.target.releasePointerCapture(e.pointerId);
    } catch (_) {}
  }
};

const userInteracted = (idx) => {
  hasInteracted.value[idx] = true;
};

// Intro animation when section scrolls into view
let animFrames = [];
let observer = null;

const runIntroAnimation = (idx) => {
  if (hasInteracted.value[idx]) return;

  const startTime = performance.now();
  const duration = 2400; // 2.4s sweep
  const startPos = 50;

  const step = (now) => {
    if (hasInteracted.value[idx]) return;
    const elapsed = now - startTime;
    if (elapsed < duration) {
      const progress = elapsed / duration;
      // Smooth sine wave swing: 50 -> 32 -> 68 -> 50
      const wave = Math.sin(progress * Math.PI * 2);
      sliderPositions.value[idx] = Math.round(startPos + wave * 18);
      animFrames[idx] = requestAnimationFrame(step);
    } else {
      sliderPositions.value[idx] = 50;
    }
  };
  animFrames[idx] = requestAnimationFrame(step);
};

onMounted(() => {
  if ('IntersectionObserver' in window && resultsSectionRef.value) {
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            runIntroAnimation(0);
            runIntroAnimation(1);
          }, 400);
          if (observer) observer.disconnect();
        }
      });
    }, { threshold: 0.2 });
    observer.observe(resultsSectionRef.value);
  } else {
    setTimeout(() => {
      runIntroAnimation(0);
      runIntroAnimation(1);
    }, 800);
  }
});

onBeforeUnmount(() => {
  if (observer) observer.disconnect();
  animFrames.forEach(f => cancelAnimationFrame(f));
});
</script>

<template>
  <div>
    <!-- Real Results & Reviews -->
    <section ref="resultsSectionRef">
      <div class="wrap">
        <div class="center">
          <h2>Real results, in her words</h2>
          <p class="lede">Every photo is shared with the client’s written permission, with no filters.</p>
        </div>

        <div class="res-grid">
          <article 
            v-for="(res, idx) in (results && results.length ? results : defaultResults)" 
            :key="res.id || idx" 
            class="res"
          >
            <!-- Interactive Before / After Comparison Slider -->
            <div 
              class="ba"
              :ref="el => setSliderRef(el, idx)"
              @pointerdown="startDrag($event, idx)"
              @pointermove="onDrag($event, idx)"
              @pointerup="stopDrag($event, idx)"
              @pointercancel="stopDrag($event, idx)"
            >
              <!-- Before Image (Base) -->
              <img 
                :src="getImages(res, idx).before" 
                :alt="`${res.client_name} Before visit`" 
                class="ba-img"
                draggable="false"
              />
              <span class="ba-pill ba-pill-before">Before (client photo)</span>

              <!-- After Image (Overlay with horizontal clip) -->
              <div 
                class="ba-overlay"
                :style="{ clipPath: `inset(0 0 0 ${sliderPositions[idx]}%)` }"
              >
                <img 
                  :src="getImages(res, idx).after" 
                  :alt="`${res.client_name} After visit`" 
                  class="ba-img"
                  draggable="false"
                />
                <span class="ba-pill ba-pill-after">After (client photo)</span>
              </div>

              <!-- Draggable Divider Line & Knob -->
              <div 
                class="ba-handle"
                :style="{ left: `${sliderPositions[idx]}%` }"
              >
                <div class="ba-handle-line"></div>
                <div class="ba-handle-knob" title="Drag to compare before and after">
                  <svg viewBox="0 0 24 24" class="ba-handle-icon" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 7l-5 5 5 5M16 7l5 5-5 5"/>
                  </svg>
                </div>
              </div>

              <!-- Range for accessibility -->
              <input 
                type="range" 
                min="0" 
                max="100" 
                v-model.number="sliderPositions[idx]" 
                class="ba-sr-range"
                :aria-label="`Compare before and after for ${res.client_name}`"
                @input="userInteracted(idx)"
              />
            </div>

            <div class="res-body">
              <h3>{{ res.client_name }}</h3>
              <dl>
                <dt>Visit</dt>
                <dd>{{ res.plan_info || res.service_tag || 'FMZ Glow Facial' }}</dd>
                <dt>Concern</dt>
                <dd>{{ res.concern || 'Dull, tired skin' }}</dd>
              </dl>
              <blockquote>{{ res.quote }}</blockquote>
            </div>
          </article>
        </div>

        <p class="small center" style="margin-top: 16px;">
          Results vary with skin type, routine and number of visits. These are cosmetic services, not medical treatments.
        </p>

        <!-- Reviews Grid -->
        <div class="rev-grid">
          <figure 
            v-for="(rev, rIdx) in (reviews && reviews.length ? reviews.slice(0, 3) : defaultReviews)" 
            :key="rev.id || rIdx" 
            class="rev" 
            style="margin: 0;"
          >
            <div class="stars" aria-label="5 out of 5">★★★★★</div>
            <p>{{ rev.quote }}</p>
            <cite>{{ rev.client_name }}</cite>
          </figure>
        </div>
      </div>
    </section>

    <!-- Checklist / Hygiene Section -->
    <section style="padding-top: 0;">
      <div class="wrap hyg">
        <div>
          <h2>Our checklist, every visit</h2>
          <p class="lede">
            Before the Galvanic Spa, your specialist asks about pregnancy, implanted electrical devices, metal implants and skin conditions, and skips that step if it isn’t right for you.
          </p>
        </div>
        <div class="checks">
          <div>Sealed, genuine Nu Skin products</div>
          <div>Fresh towels and headband</div>
          <div>Devices cleaned before every visit</div>
          <div>Skin and health check first</div>
        </div>
      </div>
    </section>
  </div>
</template>
