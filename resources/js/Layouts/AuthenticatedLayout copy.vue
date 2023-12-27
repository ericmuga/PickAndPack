<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Collapsible Sidebar -->
    <div class="w-64 overflow-hidden bg-gray-400" :class="{ 'hidden': !sidebarOpen }">
      <!-- Sidebar Header -->
      <div class="p-4">
        <button @click="toggleSidebar" class="text-white">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
          </svg>
        </button>
      </div>

      <!-- Main Navigation -->
      <nav>
        <div @click="toggleSubMenu('dashboard')" class="px-4 py-2 cursor-pointer">
          <span class="text-white">Dashboard</span>
          <span v-if="subMenuOpen['dashboard']" class="ml-2 text-white">&#9660;</span>
          <span v-else class="ml-2 text-white">&#9654;</span>
        </div>
        <div v-show="subMenuOpen['dashboard']">
          <div class="py-2 pl-8 cursor-pointer">Submenu 1</div>
          <div class="py-2 pl-8 cursor-pointer">Submenu 2</div>
        </div>

        <div @click="toggleSubMenu('orders')" class="px-4 py-2 cursor-pointer">
          <span class="text-white">Orders</span>
          <span v-if="subMenuOpen['orders']" class="ml-2 text-white">&#9660;</span>
          <span v-else class="ml-2 text-white">&#9654;</span>
        </div>
        <div v-show="subMenuOpen['orders']">
          <div class="py-2 pl-8 cursor-pointer">Submenu 1</div>
          <div class="py-2 pl-8 cursor-pointer">Submenu 2</div>
        </div>

        <!-- Add more main navigation items as needed -->
      </nav>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Top Navigation Bar -->
      <header class="p-4 bg-gray-800">
        <button @click="toggleSidebar" class="text-white">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
          </svg>
        </button>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 overflow-x-hidden overflow-y-auto bg-gray-100">
        <!-- Page Heading -->
        <header class="bg-white shadow" v-if="$slots.header">
          <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <slot name="header" />
          </div>
        </header>

        <!-- Page Content -->
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const sidebarOpen = ref(true);
const subMenuOpen = ref({
  dashboard: false,
  orders: false,
  // Add more submenu states as needed
});

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

const toggleSubMenu = (menuKey) => {
  subMenuOpen.value[menuKey] = !subMenuOpen.value[menuKey];
};
</script>

<style scoped>
/* Add styles for the sidebar, transition, etc. here */
/* Example styles: */
.w-64 {
  transition: width 0.3s ease-in-out;
}

.hidden {
  width: 0;
  overflow: hidden;
}

.cursor-pointer:hover {
  background-color: gray;
}
</style>
