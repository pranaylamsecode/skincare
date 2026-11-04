<script setup>
defineProps({
  reviews: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <section class="safety">
    <div class="wrap safe-grid">
      <div>
        <div class="eyebrow">Hygiene and safety</div>
        <h2>Our checklist, every visit</h2>
        <div class="checks">
          <div>Sealed, genuine Nu Skin products</div>
          <div>Fresh towels and headband</div>
          <div>Devices cleaned before every visit</div>
          <div>Skin and health check first</div>
        </div>
        <p class="safe-note">Before using the Galvanic Spa, your specialist asks about pregnancy, implanted electrical devices, metal implants and skin conditions, and skips that step if it isn’t right for you.</p>
      </div>
      <div>
        <div class="eyebrow">Reviews</div>
        <h2>In her words</h2>
        <div v-for="rev in reviews" :key="rev.id" class="review">
          <p>{{ rev.quote }}</p>
          <b>{{ rev.client_name }}</b>
        </div>
      </div>
    </div>
  </section>
</template>
