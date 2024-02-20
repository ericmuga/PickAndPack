<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Collapsible Sidebar -->
    <div class="w-64 overflow-hidden bg-red-800" :class="{ 'hidden': !sidebarOpen }">
      <!-- Sidebar Header -->
      <div class="flex justify-center flex-auto p-5 ">
        <img src="/fcl1.png" alt="logo" class="h-10 text-center">
        <!-- <button @click="toggleSidebar" class="text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
          </svg>
        </button> -->

      </div>

      <!-- Main Navigation -->
      <nav>
        <div @click="toggleSubMenu('dashboard')" class="px-4 py-2 cursor-pointer">
          <span class="text-white">Dashboard</span>
          <span v-if="subMenuOpen['dashboard']" class="ml-2 text-white">&#9660;</span>
          <span v-else class="ml-2 text-white">&#9654;</span>
        </div>
        <div v-show="subMenuOpen['dashboard']">
          <div class="py-2 pl-8 cursor-pointer"><Link :href="route('dashboard')">Dashboard</Link></div>

          <!-- <div class="py-2 pl-8 cursor-pointer">Submenu 2</div> -->
        </div>

        <div @click="toggleSubMenu('orders')" class="px-4 py-2 cursor-pointer">
          <span class="text-white">Orders</span>
          <span v-if="subMenuOpen['orders']" class="ml-2 text-white">&#9660;</span>
          <span v-else class="ml-2 text-white">&#9654;</span>
        </div>
            <div v-show="subMenuOpen['orders']">
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('confirmations.index')" :active="route().current('confirmations.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Registry</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('orders.lines')" :active="route().current('orders.lines')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Prepacks</Link></div>
                <div class="block w-full px-2 py-2 pl-8 font-semibold text-white rounded cursor-pointer hover:bg-gray-600">
                <p  class="p-2 font-semibold tracking-wide rounded-md hover:cursor-pointer" @click="toggleAssignment()">Assignment</p>
                    <Accordion  v-show="showAssignment">

                    <AccordionTab header="Station A" class="w-full" >
                        <AssignmentLinks station="a"/>
                        </AccordionTab>
                        <AccordionTab header="Station B" >
                            <AssignmentLinks station="b"/>
                        </AccordionTab>
                    </Accordion>

                </div>
                <div class="py-2 pl-8 cursor-pointer"> <Link :href="route('assembly.index')" :active="route().current('assembly.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Assembly</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('packingSession.index')" :active="route().current('packingSession.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Packing</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('loadingSession.index')" :active="route().current('loadingSession.index')"  class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Loading</Link></div>
           </div>
        <div @click="toggleSubMenu('stocks')" class="px-4 py-2 cursor-pointer">
            <span class="text-white">Stocks</span>
            <span v-if="subMenuOpen['reports']" class="ml-2 text-white">&#9660;</span>
            <span v-else class="ml-2 text-white">&#9654;</span>
            </div>


        <div v-show="subMenuOpen['stocks']">
          <div class="py-2 pl-8 cursor-pointer"><Link :href="route('stocks.index')" :active="route().current('stocks.index')" >Stock Take</Link></div>

          <!-- <div class="py-2 pl-8 cursor-pointer">Submenu 2</div> -->
        </div>
        <div @click="toggleSubMenu('admin')" class="px-4 py-2 cursor-pointer">
          <span class="text-white">Admin</span>
          <span v-if="subMenuOpen['admin']" class="ml-2 text-white">&#9660;</span>
          <span v-else class="ml-2 text-white">&#9654;</span>
        </div>
            <div v-show="subMenuOpen['admin']">
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('items.index')" :active="route().current('items.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Items</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('prepacks.index')" :active="route().current('prepacks.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Configure Prepacks</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('makeCall')" :active="route().current('makeCall')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Bot API</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('users.index')" :active="route().current('users.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Users</Link></div>
                <div class="py-2 pl-8 cursor-pointer"> <Link :href="route('packingVessel.index')" :active="route().current('packingVessel.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Vessels</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('vehicles.index')" :active="route().current('vehicles.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Vehicles</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('permissions.index')" :active="route().current('permissions.index')"  class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Permissions</Link></div>
                <div class="py-2 pl-8 cursor-pointer"><Link :href="route('roles.index')" :active="route().current('roles.index')"  class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Roles</Link></div>
           </div>



         <div @click="toggleSubMenu('reports')" class="px-4 py-2 cursor-pointer">
            <span class="text-white">Reports</span>
            <span v-if="subMenuOpen['reports']" class="ml-2 text-white">&#9660;</span>
            <span v-else class="ml-2 text-white">&#9654;</span>
            </div>


        <div v-show="subMenuOpen['reports']">
          <div class="py-2 pl-8 cursor-pointer"><Link :href="route('linePrepacks.index')" :active="route().current('linePrepacks.index')">Prepacked Orders</Link></div>

          <!-- <div class="py-2 pl-8 cursor-pointer">Submenu 2</div> -->
        </div>

        <div @click="toggleSubMenu('user')" class="px-4 py-2 cursor-pointer">
          <span class="text-white">{{ $page.props.auth.user.name }}</span>
          <span v-if="subMenuOpen['user']" class="ml-2 text-white">&#9660;</span>
          <span v-else class="ml-2 text-white">&#9654;</span>
        </div>
            <div v-show="subMenuOpen['user']">
                <div class="py-2 pl-8 cursor-pointer"> <Link  :href="route('profile.edit')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Profile</Link></div>
                <div class="py-2 pl-8 cursor-pointer"> <Link  :href="route('logout')" method="post" as="button" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Log Out</Link></div>
               </div>
        <!-- Add more main navigation items as needed -->
      </nav>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Top Navigation Bar -->
      <header class="p-4 bg-red-800">

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
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import { ref,onMounted,onBeforeUnmount } from 'vue';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import AssignmentLinks from '@/Components/AssignmentLinks.vue';

let showAssignment=ref(false);

const toggleAssignment=()=>showAssignment.value=!showAssignment.value
const sidebarOpen = ref(false);

const handleScreenSizeChange = () => {
  if (window.matchMedia('(min-width: 768px)').matches) {
    sidebarOpen.value = true;
  } else {
    sidebarOpen.value = false;
  }
};

const addResizeListener = () => {
  window.addEventListener('resize', handleScreenSizeChange);
};

const removeResizeListener = () => {
  window.removeEventListener('resize', handleScreenSizeChange);
};

onMounted(() => {
  handleScreenSizeChange();
  addResizeListener();
});

onBeforeUnmount(() => {
  removeResizeListener();
});


const subMenuOpen = ref({
  dashboard: false,
  orders: false,
  assignments:false,
  admin:false,
  user:false,
  reports:false,

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
