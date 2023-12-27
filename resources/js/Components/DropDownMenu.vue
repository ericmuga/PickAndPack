<!-- Dropdown.vue -->
<template>
  <div :class="{ 'block': isOpen, 'hidden': !isOpen }">
    <button @click="$emit('toggle')">
      {{ title }}
      <svg v-if="isOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        class="inline-block w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        class="inline-block w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <div class="py-2 pl-4">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    isOpen: Boolean,
  },
};
</script>
