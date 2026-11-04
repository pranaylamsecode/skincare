<script setup>
defineProps({
  whatsappNumber: {
    type: String,
    default: '+917303706047'
  },
  whatsappMessage: {
    type: String,
    default: "Hi FMZ Glow, I'd like to know more about your at-home skincare services."
  }
});
</script>

<template>
  <a 
    class="wa-float" 
    :href="`https://wa.me/${whatsappNumber.replace(/[^0-9+]/g, '')}?text=${encodeURIComponent(whatsappMessage)}`" 
    target="_blank" 
    rel="noopener" 
    aria-label="Chat with FMZ Glow on WhatsApp"
  >
    <svg viewBox="0 0 32 32" aria-hidden="true">
      <path d="M16.003 3C8.83 3 3 8.83 3 16c0 2.29.6 4.53 1.74 6.5L3 29l6.67-1.7A12.95 12.95 0 0 0 16 29c7.17 0 13-5.83 13-13S23.17 3 16.003 3zm0 23.7c-1.95 0-3.86-.53-5.53-1.52l-.4-.23-3.96 1.01 1.06-3.86-.26-.4A10.66 10.66 0 0 1 5.3 16c0-5.9 4.8-10.7 10.7-10.7S26.7 10.1 26.7 16s-4.8 10.7-10.7 10.7zm5.87-8c-.32-.16-1.9-.94-2.2-1.05-.3-.1-.5-.16-.72.16-.22.32-.83 1.05-1.02 1.27-.19.22-.38.24-.7.08-.32-.16-1.36-.5-2.59-1.6-.96-.85-1.6-1.9-1.79-2.22-.19-.32-.02-.5.14-.66.14-.14.32-.38.48-.57.16-.19.22-.32.32-.54.1-.22.05-.4-.03-.57-.08-.16-.72-1.74-.99-2.38-.26-.63-.52-.54-.72-.55h-.62c-.22 0-.57.08-.86.4-.3.32-1.13 1.1-1.13 2.69s1.16 3.12 1.32 3.34c.16.22 2.28 3.48 5.52 4.88.77.33 1.37.53 1.84.68.77.25 1.48.21 2.04.13.62-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.3-.21-.62-.37z"/>
    </svg>
  </a>
</template>
