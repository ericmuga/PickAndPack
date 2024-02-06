<!-- NavLink.vue -->
<template>
  <Link :href="href" :class="{ 'bg-gray-600': active, 'text-white': active, 'text-gray-300': !active }"
    class="block px-4 py-2 rounded hover:bg-gray-500">
    <slot></slot>
  </Link>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
  components: {
    Link,
  },
  props: {
    href: String,
    active: Boolean,
  },
};
</script>
