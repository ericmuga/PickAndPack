<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/inertia-vue3';

const showingNavigationDropdown = ref(false);

let isOpen=ref(false);
let dropDownIsOpen= ref(false);
let dropDown2IsOpen= ref(false);
let dropDown3IsOpen= ref(false);
let dropDown4IsOpen= ref(false);
let dropDown5IsOpen= ref(false);
let dropDown6IsOpen= ref(false);

const openOrCloseMenu=()=>{isOpen.value=!isOpen.value};
const openOrCloseDropDownMenu=()=>{dropDownIsOpen.value=!dropDownIsOpen.value};
const openOrCloseDropDown2Menu=()=>{dropDown2IsOpen.value=!dropDown2IsOpen.value};
const openOrCloseDropDown3Menu=()=>{dropDown3IsOpen.value=!dropDown3IsOpen.value};
const openOrCloseDropDown4Menu=()=>{dropDown4IsOpen.value=!dropDown4IsOpen.value};
const openOrCloseDropDown5Menu=()=>{dropDown5IsOpen.value=!dropDown5IsOpen.value};
const openOrCloseDropDown6Menu=()=>{dropDown6IsOpen.value=!dropDown6IsOpen.value};

</script>

<template>
    <div>
      <!--nav bar -->
        <div class="bg-gray-400">
        <!-- navbar right -->
          <div class="flex justify-between px-4 py-3 sm:items-center">
              <div class="flex items-center justify-between space-x-2">

                   <Link :href="route('dashboard')"><img src="/fcl1.png" alt="logo" class="h-10"> </Link>
              </div>
              <div class="md:hidden">
                <button @click="openOrCloseMenu" class="text-gray-700 focus:text-white focus:outline-none hover:text-white">
                   <svg  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="w-6 h-6 fill-current">
                     <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                     <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

            <div class="px-4 py-2 md:flex" :class="isOpen?'block':'hidden'">
              <Link :href="route('confirmations.index')" :active="route().current('confirmations.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Registry</Link>

              <Link :href="route('assignment.index')" :active="route().current('assignment.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Assignment</Link>

              <div class="px-2 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-600 md:p-0 md:mx-2">
                <button class="flex items-center justify-between space-x-2" @click="openOrCloseDropDown4Menu" >Assembly
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path v-if="!dropDown4IsOpen" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                </button>
                <div class="flex-col py-2 space-y-1 md:flex" >
                   <Link :href="route('assembly.index')" :active="route().current('assembly.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown4IsOpen?'block':'hidden'"
                   >Assemble Order </Link>

                   <Link :href="route('picks.index')" :active="route().current('picks.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown4IsOpen?'block':'hidden'"
                   >Assemble Pick </Link>


                </div>
               </div>

              <!-- <Link :href="route('packing.index')" :active="route().current('packing.index')" class="block px-2 py-1 font-semibold text-white rounded hover:bg-gray-600 ">Packing</Link> -->

              <div class="px-2 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-600 md:p-0 md:mx-2">
                <button class="flex items-center justify-between space-x-2" @click="openOrCloseDropDown5Menu" >Packing
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path v-if="!dropDown5IsOpen" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                </button>
                <div class="flex-col py-2 space-y-1 md:flex" >
                   <Link :href="route('packing.index')" :active="route().current('orders.lines')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown5IsOpen?'block':'hidden'"
                   >Pack Order </Link>

                   <Link :href="route('packingSession.index')" :active="route().current('packingSession.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown5IsOpen?'block':'hidden'"
                   >Packing Sessions </Link>
                 </div>
               </div>

               <div class="px-2 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-600 md:p-0 md:mx-2">
                <button class="flex items-center justify-between space-x-2" @click="openOrCloseDropDown6Menu" >Loading
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path v-if="!dropDown6IsOpen" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                </button>
                <div class="flex-col py-2 space-y-1 md:flex" >
                  <Link :href="route('loadingSession.index')" :active="route().current('loadingSession.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown6IsOpen?'block':'hidden'"
                   >Loading Sessions
                 </Link>

                 </div> <Link :href="route('loadingSession.index')" :active="route().current('loadingSession.Show')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown6IsOpen?'block':'hidden'"
                   >Load Truck </Link>
               </div>




              <div class="px-2 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-600 md:p-0 md:mx-2">
                <button class="flex items-center justify-between space-x-2" @click="openOrCloseDropDown2Menu" >Prepacks
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path v-if="!dropDown2IsOpen" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                </button>
                <div class="flex-col py-2 space-y-1 md:flex" >
                   <Link :href="route('orders.lines')" :active="route().current('orders.lines')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown2IsOpen?'block':'hidden'"
                   >Allocate Prepacks </Link>

                   <Link :href="route('linePrepacks.index')" :active="route().current('linePrepacks.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown2IsOpen?'block':'hidden'"
                   >Prepacked Orders </Link>
                 </div>
               </div>

                <div class="px-2 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-600 md:p-0 md:mx-2">
                <button class="flex items-center justify-between space-x-2" @click="openOrCloseDropDown3Menu" >Admin
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path v-if="!dropDown3IsOpen" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                </button>
                <div class="flex-col py-2 space-y-1 md:flex" >
                   <Link :href="route('items.index')" :active="route().current('items.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown3IsOpen?'block':'hidden'"
                   >Items </Link>
                   <Link :href="route('users.index')" :active="route().current('users.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown3IsOpen?'block':'hidden'"
                   >Users </Link>

                   <Link :href="route('items.index')" :active="route().current('items.index')"
                   class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2 "
                   :class="dropDown3IsOpen?'block':'hidden'"
                   >Vehicles </Link>


                   <Link :href="route('prepacks.index')" :active="route().current('prepacks.index')"
                      class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2"
                     :class="dropDown3IsOpen?'block':'hidden'"
                      >Define Prepacks </Link>

                </div>
               </div>

              <Link :href="route('stocks.index')" :active="route().current('stocks.index')" class="block px-2 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-600 md:p-0 md:mx-2">Stocks </Link>



              <div class="px-2 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-600 md:p-0 md:mx-2">
                <button class="flex items-center justify-between space-x-2" @click="openOrCloseDropDownMenu" >{{ $page.props.auth.user.name }}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                      <path v-if="!dropDownIsOpen" stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      <path v-else stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                </button>
                <div class="flex-col py-2 space-y-1 md:flex" >
                    <Link  :href="route('profile.edit')" :class="dropDownIsOpen?'block':'hidden'"
                         class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2">Profile </Link>
                    <Link  :href="route('logout')" method="post" as="button"
                     :class="dropDownIsOpen?'block':'hidden'"
                        class="px-3 py-1 mt-1 font-semibold text-white rounded hover:bg-gray-100 md:p-0 md:mx-2">Logout </Link>
                </div>
              </div>


            </div>
        </div>
        <!--navbar left -->
          <div>

          </div>
        </div>
    <!--end of nav bar -->

        <div class="min-h-screen bg-gray-100">



            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
