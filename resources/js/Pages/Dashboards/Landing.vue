
<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
  <div class="flex flex-col w-auto h-auto gap-3 fade-in place-items-center">
    <Link :href="route('confirmations.index')" class="w-full p-2 font-semibold tracking-wide text-center rounded-md btn-registry">Registry</Link>
    <Link :href="route('assignment.create')"  class="w-full p-2 font-semibold tracking-wide text-center rounded-md btn-assignment">Assignment</Link>
    <Link :href="route('assembly.index')"  class="w-full p-2 font-semibold tracking-wide text-center rounded-md btn-assembly">Assembly</Link>
    <Link :href="route('packingSession.index')"  class="w-full p-2 font-semibold tracking-wide text-center rounded-md btn-packing">Packing</Link>
    <Link :href="route('loadingSession.index')"  class="w-full p-2 font-semibold tracking-wide text-center rounded-md btn-admin">Loading</Link>
    <!-- <Link :href="route('reports')" class="btn-reports">Reports</Link> -->
  </div>
  </GuestLayout>
</template>

<script setup>
import { Link,Head } from '@inertiajs/inertia-vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

// const route = useRoute();

// You may not need this goTo method anymore with Link
// but you can keep it for other functionality if needed
// const goTo = (page) => {
//    console.log(`Navigating to ${page} page`);
//  }
</script>

<style scoped>
.fade-in {
  transition: opacity 0.5s ease-in-out;
  opacity: 1;
}

.btn-registry { background-color: #FF5733; } /* Orange */
.btn-assignment { background-color: #FFC300; } /* Yellow */
.btn-assembly { background-color: #3498DB; } /* Blue */
.btn-packing { background-color: #27AE60; } /* Green */
.btn-admin { background-color: #8E44AD; } /* Purple */
.btn-reports { background-color: #34495E; } /* Dark Gray */

/* Add other button styles as needed */
</style>
