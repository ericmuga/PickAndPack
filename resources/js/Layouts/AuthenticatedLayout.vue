<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/inertia-vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
          
<nav class="  bg-white border-gray-200 dark:bg-gray-900 dark:border-gray-700 items-center">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
    <a :href="route('dashboard')" class="flex items-center">
        <img src="/fcl1.png" class="h-8 mr-3" alt="FCL" />
       Dashboard   
         
    </a>

    <button data-collapse-toggle="navbar-multi-level" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-multi-level" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
    </button>
    <div class="hidden w-full md:block md:w-auto" id="navbar-multi-level">
      <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
        <li>
          
        </li>
        <li>
          <NavLink :href="route('confirmations.index')" :active="route().current('confirmations.index')">
                                Confirmation
                            </NavLink>
        </li>
        
        <li>
           <NavLink :href="route('order.pack')" :active="route().current('order.pack')">
                                    Assembly
                                </NavLink>
        </li>
        <li>
           <NavLink :href="route('picks.index')" :active="route().current('picks.index')">
                                    Assemble Picks
                                </NavLink>
        </li>
        <li>
         <NavLink :href="route('packing.index')" :active="route().current('packing.index')">
                                    Packing
          </NavLink>
        </li>
     
        
        
           <li>
            <NavLink :href="route('orders.lines')" :active="route().current('orders.lines')">
                                    Allocate Prepacks
                                </NavLink>
          </li>
          <li>
             <NavLink :href="route('linePrepacks.index')" :active="route().current('linePrepacks.index')">
                                    Prepacked Orders
                                </NavLink>
          </li>
          <li>
             <NavLink :href="route('stocks.index')" :active="route().current('stocks.index')">
                                   Stocks
                                </NavLink>
          </li>
          
                 
              
    
        <li>
            <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar" class="flex items-center justify-between w-full py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent">Admin <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
  </svg>
  </button>
            <!-- Dropdown menu -->
            <div id="dropdownNavbar" class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">
                  <li>
            <NavLink :href="route('items.index')" :active="route().current('items.index')">
                                    Items
                                </NavLink>
         </li>
          <li>
            <NavLink :href="route('prepacks.index')" :active="route().current('prepacks.index')">
                            Prepacks
                        </NavLink>
          </li>

                  <li>
                    <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                  </li>
                  <li>
                     <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                      </DropdownLink>
                                    
                  </li>
                  
                </ul>
                
            </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
 

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
