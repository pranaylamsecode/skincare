<script setup>
defineProps({
  text: {
    type: String,
    default: 'Launch offer: <b>FMZ Glow Facial ₹999</b> <s>₹1,199</s>, now in Ahmedabad, Gandhinagar, Surat and Rajkot'
  }
});
</script>

<template>
  <div class="banner" v-html="text"></div>
</template>
