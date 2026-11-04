<script setup>
const props = defineProps({
  plans: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['select-plan']);

const selectPlan = (planTitle) => {
  emit('select-plan', planTitle);
};

const formatPrice = (price) => {
  return '₹' + Math.round(Number(price)).toLocaleString('en-IN');
};
</script>

<template>
  <section class="plans" id="plans">
    <div class="wrap plans-grid">
      <div class="plans-copy">
        <div class="eyebrow">Glow plans</div>
        <h2>Beautiful skin is a habit, not a one-off</h2>
        <p>Regular care with the same trusted specialist keeps your routine on track. Plans save you money and keep your next visit booked.</p>
      </div>

      <article 
        v-for="plan in plans" 
        :key="plan.id" 
        class="plan" 
        :class="{ first: plan.is_featured }"
      >
        <span 
          v-if="plan.tag" 
          class="tag" 
          :class="plan.is_featured ? 'tan' : ''"
          :style="!plan.is_featured ? 'background:var(--bg);color:var(--ink)' : ''"
        >
          {{ plan.tag }}
        </span>

        <h3>{{ plan.title }}</h3>

        <div v-if="Number(plan.price) > 0" class="price">
          {{ formatPrice(plan.price) }}
          <small v-if="plan.price_subtext">{{ plan.price_subtext }}</small>
        </div>

        <p>{{ plan.description }}</p>

        <a 
          v-if="plan.is_featured" 
          class="link" 
          href="#book" 
          @click.prevent="selectPlan(plan.title)"
        >
          Ask about this plan →
        </a>
        <a 
          v-else 
          class="link" 
          href="#expert"
        >
          Notify me →
        </a>
      </article>
    </div>
  </section>
</template>
