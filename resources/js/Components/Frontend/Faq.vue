<script setup>
const props = defineProps({
  faqs: {
    type: Array,
    default: () => []
  }
});

const defaultFaqs = [
  {
    question: 'Which areas do you cover?',
    answer: 'Ahmedabad, Gandhinagar, Surat and Rajkot. Enter your pincode when booking and we’ll tell you straight away if a specialist covers your area.'
  },
  {
    question: 'Is there a travel charge?',
    answer: 'No charge within 8 km of your specialist. If you’re further away, we tell you the exact amount on WhatsApp before you confirm.'
  },
  {
    question: 'Can I reschedule or cancel?',
    answer: 'Yes, free of charge up to 4 hours before your visit. Just reply to your confirmation message on WhatsApp.'
  },
  {
    question: 'Is it suitable for sensitive skin or acne?',
    answer: 'Your specialist checks your skin first and adjusts or skips steps that aren’t right for you. For active skin conditions, please speak to a dermatologist first.'
  },
  {
    question: 'Can I have a facial during pregnancy or while breastfeeding?',
    answer: 'Tell us when booking. The Galvanic Spa step is skipped during pregnancy, and your specialist will suggest a gentle alternative.'
  },
  {
    question: 'What do I need to prepare?',
    answer: 'A comfortable place to lie down near a power socket, and clean, makeup-free skin if possible. Your specialist brings everything else.'
  },
  {
    question: 'How do I pay?',
    answer: 'After your visit, by UPI or cash. You can also pay online when booking if you prefer.'
  }
];
</script>

<template>
  <section id="faq">
    <div class="wrap">
      <div class="center">
        <h2>Good to know</h2>
      </div>

      <div class="faq">
        <template v-if="faqs && faqs.length">
          <details v-for="faq in faqs" :key="faq.id">
            <summary>{{ faq.question }}</summary>
            <p>{{ faq.answer }}</p>
          </details>
        </template>
        <template v-else>
          <details v-for="(faq, i) in defaultFaqs" :key="i">
            <summary>{{ faq.question }}</summary>
            <p>{{ faq.answer }}</p>
          </details>
        </template>
      </div>
    </div>
  </section>
</template>
