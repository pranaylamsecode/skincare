<script setup>
defineProps({
  faqs: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <section class="faq" id="faq">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Good to know</div>
        <h2>Frequently asked questions</h2>
      </div>
      <div class="faq-list">
        <details v-for="faq in faqs" :key="faq.id">
          <summary>{{ faq.question }}</summary>
          <p>{{ faq.answer }}</p>
        </details>
      </div>
    </div>
  </section>
</template>
