<script setup>
defineProps({
  concerns: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <section class="concerns" id="skin">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">What’s on your mind?</div>
        <h2>Tell us how your skin feels</h2>
        <p>Every woman’s skin is different. Pick what sounds like you, and we’ll suggest where to start.</p>
      </div>
      <div class="concern-grid">
        <article 
          v-for="item in concerns" 
          :key="item.id" 
          class="concern" 
          :class="{ dark: item.is_dark }"
        >
          <h3>{{ item.quote }}</h3>
          <p>{{ item.solution }}</p>
          <a class="link" :href="item.link_target">{{ item.link_text }}</a>
        </article>
      </div>
    </div>
  </section>
</template>
