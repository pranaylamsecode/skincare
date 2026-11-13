<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import TopBar from '@/Components/Frontend/TopBar.vue';
import Header from '@/Components/Frontend/Header.vue';
import Hero from '@/Components/Frontend/Hero.vue';
import Concerns from '@/Components/Frontend/Concerns.vue';
import Services from '@/Components/Frontend/Services.vue';
import HowItWorks from '@/Components/Frontend/HowItWorks.vue';
import Specialists from '@/Components/Frontend/Specialists.vue';
import Results from '@/Components/Frontend/Results.vue';
import Booking from '@/Components/Frontend/Booking.vue';
import Faq from '@/Components/Frontend/Faq.vue';
import Footer from '@/Components/Frontend/Footer.vue';
import MobileCta from '@/Components/Frontend/MobileCta.vue';
import WhatsAppFloat from '@/Components/Frontend/WhatsAppFloat.vue';

const props = defineProps({
  services: { type: Array, default: () => [] },
  specialists: { type: Array, default: () => [] },
  plans: { type: Array, default: () => [] },
  devices: { type: Array, default: () => [] },
  dailyProducts: { type: Array, default: () => [] },
  wellnessProducts: { type: Array, default: () => [] },
  concerns: { type: Array, default: () => [] },
  results: { type: Array, default: () => [] },
  reviews: { type: Array, default: () => [] },
  faqs: { type: Array, default: () => [] },
  settings: { type: Object, default: () => ({}) },
  timeSlots: { type: Array, default: () => [] }
});

const selectedService = ref('FMZ Glow Facial');
const selectedSpecialist = ref(null);
const selectedConcern = ref('');

const handleServiceSelect = (title) => {
  selectedService.value = title;
  const bookEl = document.getElementById('book');
  if (bookEl) {
    bookEl.scrollIntoView({ behavior: 'smooth' });
  }
};

const handleSpecialistSelect = (specialist) => {
  selectedSpecialist.value = specialist;
  const bookEl = document.getElementById('book');
  if (bookEl) {
    bookEl.scrollIntoView({ behavior: 'smooth' });
  }
};

const handleConcernSelect = ({ concern, service }) => {
  selectedConcern.value = concern;
  if (service) {
    selectedService.value = service;
  }
  const bookEl = document.getElementById('book');
  if (bookEl) {
    bookEl.scrollIntoView({ behavior: 'smooth' });
  }
};
</script>

<template>
  <Head title="FMZ Glow · At-home skincare & wellness by certified women specialists" />

  <div class="fmz-frontend">
    <!-- Top Announcement Banner -->
    <TopBar :text="settings.topbar_text" />

    <!-- Sticky Header & Nav -->
    <Header />

    <!-- Main Content Flow matching Client HTML Design -->
    <main id="top">
      <!-- 1. Hero with Trust 4-column Strip -->
      <Hero 
        :title="settings.hero_title"
        :lead="settings.hero_lead"
        :whatsapp-number="settings.whatsapp_number"
        :whatsapp-message="settings.whatsapp_default_message"
      />

      <!-- 2. Concerns ("Tell us how your skin feels") -->
      <Concerns 
        :concerns="concerns" 
        @select-concern="handleConcernSelect"
      />

      <!-- 3. Services & Plans ("Choose your visit") -->
      <Services 
        :services="services" 
        :plans="plans"
        @select-service="handleServiceSelect"
      />

      <!-- 4. How It Works ("Simple, from start to finish") -->
      <HowItWorks />

      <!-- 5. Specialists ("Meet your specialists" with Side-Scroll) -->
      <Specialists 
        :specialists="specialists" 
        @select-specialist="handleSpecialistSelect"
      />

      <!-- 6. Real Results, Reviews & Hygiene Checklist -->
      <Results 
        :results="results" 
        :reviews="reviews" 
      />

      <!-- 7. Booking ("Ready for your glow?") - Untouched functionality and exact wording -->
      <Booking 
        :services="services"
        :specialists="specialists"
        :time-slots="timeSlots"
        :preselected-service="selectedService"
        :preselected-specialist="selectedSpecialist"
        :preselected-concern="selectedConcern"
        :settings="settings"
      />

      <!-- 8. FAQ ("Good to know") -->
      <Faq :faqs="faqs" />
    </main>

    <!-- Footer with Social Links -->
    <Footer :settings="settings" />

    <!-- Sticky Mobile CTA Bar -->
    <MobileCta 
      :whatsapp-number="settings.whatsapp_number" 
    />

    <!-- Floating WhatsApp Action Button -->
    <WhatsAppFloat 
      :whatsapp-number="settings.whatsapp_number"
      :whatsapp-message="settings.whatsapp_default_message"
    />
  </div>
</template>
