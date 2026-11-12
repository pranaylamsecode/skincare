<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({})
  }
});

const currentYear = new Date().getFullYear();

// Instagram link
const instagramUrl = computed(() => {
  return props.settings?.social_instagram || 'https://www.instagram.com/';
});

// Facebook link
const facebookUrl = computed(() => {
  return props.settings?.social_facebook || 'https://www.facebook.com/';
});

// YouTube link
const youtubeUrl = computed(() => {
  return props.settings?.social_youtube || 'https://www.youtube.com/';
});

// WhatsApp link
const whatsappUrl = computed(() => {
  const custom = props.settings?.social_whatsapp;
  if (custom && custom.trim()) {
    return custom.startsWith('http') ? custom : `https://wa.me/${custom.replace(/[^0-9]/g, '')}`;
  }
  const phone = (props.settings?.whatsapp_number || '+917303706047').replace(/[^0-9]/g, '');
  const msg = encodeURIComponent(props.settings?.whatsapp_default_message || "Hi FMZ Glow, I'd like to know more about your at-home skincare services.");
  return `https://wa.me/${phone}?text=${msg}`;
});
</script>

<template>
  <footer class="site-footer">
    <div class="wrap">
      <!-- Social Media Icons Row -->
      <div class="footer-social-row">
        <a 
          :href="instagramUrl" 
          target="_blank" 
          rel="noopener noreferrer" 
          class="social-btn" 
          title="Follow us on Instagram"
        >
          <svg class="social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
          </svg>
          <span class="social-label">Instagram</span>
        </a>

        <a 
          :href="facebookUrl" 
          target="_blank" 
          rel="noopener noreferrer" 
          class="social-btn" 
          title="Follow us on Facebook"
        >
          <svg class="social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
          </svg>
          <span class="social-label">Facebook</span>
        </a>

        <a 
          :href="youtubeUrl" 
          target="_blank" 
          rel="noopener noreferrer" 
          class="social-btn" 
          title="Subscribe to our YouTube channel"
        >
          <svg class="social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor"></polygon>
          </svg>
          <span class="social-label">YouTube</span>
        </a>

        <a 
          :href="whatsappUrl" 
          target="_blank" 
          rel="noopener noreferrer" 
          class="social-btn" 
          title="Message us on WhatsApp"
        >
          <svg class="social-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
          <span class="social-label">WhatsApp</span>
        </a>
      </div>

      <div class="foot">
        <img src="/images/FMZ logo.jpg" alt="FMZ Glow" width="118" height="38">
        <nav aria-label="Footer">
          <a href="#services">Services</a>
          <a href="#plans">Plans</a>
          <a href="#faq">FAQ</a>
          <a href="/privacy-policy/">Privacy Policy</a>
          <a href="/terms-and-conditions/">Terms</a>
          <a href="/admin/login" style="opacity:0.6">Admin Portal</a>
        </nav>
        <p>© {{ currentYear }} FMZ India. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer-social-row {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 38px;
  padding: 10px 0 22px;
  margin-bottom: 22px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.07);
}

.social-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  color: #1a1a1a;
  transition: transform 0.2s ease, color 0.2s ease;
  min-width: 64px;
}

.social-icon {
  width: 24px;
  height: 24px;
  color: #1a1a1a;
  transition: transform 0.2s ease, color 0.2s ease, stroke 0.2s ease;
}

.social-label {
  font-size: 0.74rem;
  font-weight: 500;
  color: #4b5563;
  letter-spacing: 0.01em;
  transition: color 0.2s ease;
}

.social-btn:hover {
  transform: translateY(-2px);
}

.social-btn:hover .social-icon {
  color: #7a2e2e;
  stroke: #7a2e2e;
}

.social-btn:hover .social-label {
  color: #7a2e2e;
}

@media (max-width: 480px) {
  .footer-social-row {
    gap: 20px;
    padding-bottom: 18px;
    margin-bottom: 18px;
  }
  .social-icon {
    width: 22px;
    height: 22px;
  }
  .social-label {
    font-size: 0.68rem;
  }
  .social-btn {
    min-width: 52px;
  }
}
</style>
