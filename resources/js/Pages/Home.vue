<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import TopBar from '@/Components/Frontend/TopBar.vue';
import Header from '@/Components/Frontend/Header.vue';
import Hero from '@/Components/Frontend/Hero.vue';
import Concerns from '@/Components/Frontend/Concerns.vue';
import HowItWorks from '@/Components/Frontend/HowItWorks.vue';
import Services from '@/Components/Frontend/Services.vue';
import Plans from '@/Components/Frontend/Plans.vue';
import Expert from '@/Components/Frontend/Expert.vue';
import Scan from '@/Components/Frontend/Scan.vue';
import Products from '@/Components/Frontend/Products.vue';
import Promise from '@/Components/Frontend/Promise.vue';
import Powered from '@/Components/Frontend/Powered.vue';
import Devices from '@/Components/Frontend/Devices.vue';
import Science from '@/Components/Frontend/Science.vue';
import Specialists from '@/Components/Frontend/Specialists.vue';
import Results from '@/Components/Frontend/Results.vue';
import Safety from '@/Components/Frontend/Safety.vue';
import Booking from '@/Components/Frontend/Booking.vue';
import Faq from '@/Components/Frontend/Faq.vue';
import Footer from '@/Components/Frontend/Footer.vue';
import WhatsAppFloat from '@/Components/Frontend/WhatsAppFloat.vue';
import MobileCta from '@/Components/Frontend/MobileCta.vue';

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

const selectedService = ref('Nu Skin LumiSpa Glow Facial');
const selectedSpecialist = ref(null);

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
</script>

<template>
  <Head title="FMZ Glow | At-Home Skincare by FMZ India" />

  <div class="fmz-frontend">
    <!-- Top Announcement Bar -->
    <TopBar :text="settings.topbar_text" />

    <!-- Sticky Header & Nav -->
    <Header />

    <!-- Main Content -->
    <main>
      <Hero 
        :eyebrow="settings.hero_eyebrow"
        :title="settings.hero_title"
        :lead="settings.hero_lead"
        :tagline="settings.hero_tagline"
      />

      <Concerns :concerns="concerns" />

      <HowItWorks />

      <Services 
        :services="services" 
        @select-service="handleServiceSelect"
      />

      <Plans 
        :plans="plans" 
        @select-plan="handleServiceSelect"
      />

      <Expert 
        :whatsapp-number="settings.whatsapp_number"
        :whatsapp-message="settings.whatsapp_default_message"
      />

      <Scan />

      <Products 
        :daily-products="dailyProducts" 
        :wellness-products="wellnessProducts" 
      />

      <Promise />

      <Powered />

      <Devices :devices="devices" />

      <Science />

      <Specialists 
        :specialists="specialists" 
        @select-specialist="handleSpecialistSelect"
      />

      <Results :results="results" />

      <Safety :reviews="reviews" />

      <Booking 
        :services="services"
        :specialists="specialists"
        :time-slots="timeSlots"
        :preselected-service="selectedService"
        :preselected-specialist="selectedSpecialist"
        :settings="settings"
      />

      <Faq :faqs="faqs" />
    </main>

    <!-- Footer -->
    <Footer :settings="settings" />

    <!-- Floating WhatsApp Widget -->
    <WhatsAppFloat 
      :whatsapp-number="settings.whatsapp_number"
      :whatsapp-message="settings.whatsapp_default_message"
    />

    <!-- Sticky Mobile CTA Button -->
    <MobileCta />
  </div>
</template>
