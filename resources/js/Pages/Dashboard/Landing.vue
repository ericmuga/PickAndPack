<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia'


import { ref, onMounted, computed } from 'vue';
import gsap from 'gsap';
// import { usePage } from '@inertiajs/vue3';

// const page = usePage();

const customerCard = ref(null);
const salesCard = ref(null);
const financialsCard = ref(null);
const administrationCard = ref(null);

const navigateTo = (component) => {
  Inertia.visit(route(component));
};

const isMobile = computed(() => {
  return window.innerWidth <= 640; // Adjust the breakpoint as needed
});

onMounted(() => {
  // Use GSAP to fade in the cards on component mount
  gsap.from([customerCard.value, salesCard.value, financialsCard.value, administrationCard.value], {
    opacity: 0,
    duration: 1,
    stagger: 0.2, // Stagger the animations for a more dynamic effect
  });
});

// Update the layout on window resize
window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth <= 640; // Adjust the breakpoint as needed
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template> -->


  <div class="max-w-sm mt-2 text-center dashboard">
    <!-- Stats Bar at the Top -->
    <div class="p-4 mt-3 text-white bg-blue-500 rounded-lg stats-bar">
      <h2 class="text-2xl font-semibold">Dashboard</h2>
      <!-- Add any other stats or information you want to display here -->
    </div>

    <!-- Cards in the Middle -->
     <div :class="{'flex-col': isMobile, 'flex-row': !isMobile}" class="items-center mt-8">
      <!-- Customer Card -->
      <div ref="customerCard" class="mb-4 card" @click="Inertia.get(route('confirmations.index'))">
        <h3 class="mb-2 text-xl font-semibold">Registry</h3>
        <!-- Add content for the Customers card -->
      </div>

      <div ref="customerCard" class="mb-4 card" @click="Inertia.get(route('assignment.index'))">
        <h3 class="mb-2 text-xl font-semibold">Assignment</h3>
        <!-- Add content for the Customers card -->
      </div>

      <!-- Sales Card -->
      <div ref="salesCard" class="mb-4 card" @click="Inertia.get(route('assembly.index'))">
        <h3 class="mb-2 text-xl font-semibold">Assembly</h3>
        <!-- Add content for the Sales card -->
      </div>

      <!-- Financials Card -->
      <div ref="salesCard" class="mb-4 card" @click="Inertia.get(route('packing.index'))">
        <h3 class="mb-2 text-xl font-semibold">Packing</h3>
        <!-- Add content for the Sales card -->
      </div>

      <!-- Administration Card -->
      <div ref="salesCard" class="mb-4 card" @click="Inertia.get(route('Loading.index'))">
        <h3 class="mb-2 text-xl font-semibold">Loading</h3>
        <!-- Add content for the Sales card -->
      </div>

      <div ref="salesCard" class="mb-4 card" @click="Inertia.get(route('assembly.index'))">
        <h3 class="mb-2 text-xl font-semibold">Admin</h3>
        <!-- Add content for the Sales card -->
      </div>
    </div>


  </div>
</AuthenticatedLayout>
</template>

<!-- </template> -->

<style scoped>
/* Add any additional styling specific to this component */
.dashboard {
  max-width: 1200px;
  margin: 0 auto;
}

.stats-bar {
  /* Customize styles for the stats bar */
}

.card {
  /* Customize styles for the cards */
  width: 100%; /* Make all cards have the same width */
  border: 1px solid #ddd;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease-in-out;
}

.card:hover {
  cursor: pointer;
}
</style>
