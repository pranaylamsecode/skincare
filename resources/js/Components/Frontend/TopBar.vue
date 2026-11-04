<script setup>
defineProps({
  text: {
    type: String,
    default: 'Launch offer: Nu Skin LumiSpa Glow Facial <b>₹999</b> <s>₹1,199</s> · limited period · T&C apply'
  }
});
</script>

<template>
  <div class="topbar" v-html="text"></div>
</template>
