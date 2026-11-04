<script setup>
import { ref } from 'vue';

defineProps({
  results: {
    type: Array,
    default: () => []
  }
});

const sliderPositions = ref({
  0: 50,
  1: 50
});

const onSliderInput = (idx, event) => {
  sliderPositions.value[idx] = event.target.value;
};
</script>

<template>
  <section class="results" id="results">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Real glow, real homes</div>
        <h2>Before and after your visit</h2>
        <p>Drag the slider on each card to compare. Every photo is shared with the client’s permission, with no filters and no heavy retouching.</p>
      </div>

      <div class="ba-grid">
        <article 
          v-for="(res, idx) in results" 
          :key="res.id" 
          class="ba-card"
        >
          <div 
            class="ba" 
            :style="{ '--pos': (sliderPositions[idx] || 50) + '%' }"
          >
            <div class="ba-layer ba-after">
              <img 
                v-if="res.image_after" 
                :src="res.image_after.startsWith('http') ? res.image_after : '/' + res.image_after" 
                :alt="`${res.client_name} skin after visit`" 
                loading="lazy" 
                onerror="this.remove()"
              >
            </div>
            <input 
              type="range" 
              min="0" 
              max="100" 
              :value="sliderPositions[idx] || 50" 
              @input="onSliderInput(idx, $event)"
              :aria-label="`Compare before and after for ${res.client_name}`"
            >
          </div>
          <div class="ba-info">
            <h3>{{ res.client_name }}</h3>
            <span v-if="res.service_tag" class="tag">{{ res.service_tag }}</span>
            <dl>
              <template v-if="res.concern">
                <dt>Concern</dt>
                <dd>{{ res.concern }}</dd>
              </template>
              <template v-if="res.plan_info">
                <dt>Plan</dt>
                <dd>{{ res.plan_info }}</dd>
              </template>
              <template v-if="res.products_used">
                <dt>Products</dt>
                <dd>{{ res.products_used }}</dd>
              </template>
            </dl>
            <blockquote>{{ res.quote }}</blockquote>
          </div>
        </article>
      </div>
      <p class="fine">Individual results vary with skin type, routine and number of visits. Photos taken in similar light and without filters. These are cosmetic services, not medical treatments.</p>
    </div>
  </section>
</template>
