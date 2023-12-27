<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import StatsTile from '@/Components/StatsTile.vue';
import { Link } from '@inertiajs/inertia-vue3'
import SpacedRule from '@/Components/SpacedRule.vue';
import DataTable from '@/Components/DataTable.vue';
import PieChart from '@/Components/PieChart.vue';
import {ref} from 'vue'
import ProgressBar from 'primevue/progressbar';
import Time from '@/Components/Time.vue'
import TopFiveBarChart from '@/Components/TopFiveBarChart.vue';

const  formatNumber=(value)=> {
      return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

defineProps({
               todays:Number,
               sectorTonnage:Object,
               assembled:Number,
               packed:Number,
               loaded:Number,
               tonnage:Number,
               pending:Number,
               refreshError:String,
               stocks:Object,
               headers:Object,
               top5Labels:Object,
               top5Weights:Array,
          })

const cdata = ref({
                    labels: ['Red', 'Blue', 'Yellow'],
                    datasets: [
                        {
                        label: 'My Dataset',
                        data: [10, 20, 30],
                        backgroundColor: ['red', 'blue', 'yellow'],
                        },
                    ],
                });

// let percent = parseFloat(pending*100/todays).toFixed(2);

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template> -->

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->
                        <div v-if="refreshError!=null">
                            {{ refreshError }}
                        </div>
  <div class="items-center justify-between w-full flex flex-row">



    <div class="grid lg:grid-cols-5 md:grid-cols-2 sm:grid-cols-1 text-center w-full">
    <!-- Sales Today Card -->
    <div class="flex flex-col justify-between p-4 mx-2 my-4 bg-white rounded-md shadow-md">
      <div>
        <h2 class="mb-2 text-xl font-semibold">Tonnage</h2>
        <!-- Your sales today data goes here -->
        <div class="text-3xl font-bold">{{ formatNumber(tonnage) }}T</div>
        <div>

        </div>
        <table  class=" w-full">
            <tr class="card" v-for="(value, groupName) in sectorTonnage" :key="groupName"  >

            <td class="px-4">{{ groupName }}</td><td class="flex justify-end"><strong>{{value}}T</strong></td>
        </tr>
         </table>

      </div>
      <!-- <div class="mt-4 text-sm text-gray-500">+5% from yesterday</div> -->
    </div>




    <!-- Top 10 Items Card -->
    <div class="flex flex-col justify-between p-4 mx-2 my-4 bg-white rounded-md shadow-md">
      <div>
        <h2 class="mb-2 text-xl font-semibold text-indigo-600">Assembled</h2>
        <!-- Your top 10 items data goes here -->
        <div class="text-3xl font-bold">{{ assembled }}T</div>
      </div>
      <!-- <div class="mt-4 text-sm text-gray-500">Best-selling items</div> -->
    </div>

    <!-- Top 10 Customers Card -->
    <div class="flex flex-col justify-between p-4 mx-2 my-4 bg-white rounded-md shadow-md">
      <div>
        <h2 class="mb-2 text-xl font-semibold text-red-600">Packed</h2>
        <!-- Your top 10 customers data goes here -->
        <div class="text-3xl font-bold">{{ packed }}T</div>
      </div>
      <!-- <div class="mt-4 text-sm text-gray-500">Loyal customers</div> -->
    </div>

    <!-- Setups Card -->
    <div class="flex flex-col justify-between p-4 mx-2 my-4 bg-white rounded-md shadow-md">
      <div>
        <h2 class="mb-2 text-xl font-semibold text-lime-500">Loaded</h2>
        <!-- Your setups data goes here -->
        <div class="text-3xl font-bold">{{ loaded }}T</div>
      </div>
      <!-- <div class="mt-4 text-sm text-gray-500">Currently active setups</div> -->
    </div>
     <div class=" flex flex-col gap-5">
      <div>
       <Time class=" mx-2  rounded-md shadow-md p-5 bg-slate-400 text-black font-bold items-center my-4 text-center"/>
        <!-- Your top 10 items data goes here -->
        <div class="card">
                    <span class="text-xs">Pending Confirmation {{ pending}}/{{ todays }}</span>
                     <div class="card">
                                <ProgressBar :value="parseFloat(pending*100/todays).toFixed(0)"> </ProgressBar>
                    </div>
                    <div class="my-5">
                                    <Link :href="route('refresh')" class="w-5 h-10 m-10 mx-auto text-center ">
                                        <!-- <span class="text-xs">Refresh</span> -->
                                       <!-- <img src="/img/refresh.png" /> -->
                                       <Button icon="pi pi-refresh" severity="warning" aria-label="Filter" />

                                    </Link>
                                </div>
     </div>

      </div>
      <!-- <div class="mt-4 text-sm text-gray-500">Best-selling items</div> -->
    </div>

  </div>


                        </div>

                        <!--end of stats bar-->

                    </div>

                        <div class="flex items-center w-full text-center">
                                     <SpacedRule class="text-center "/>

                                      <!-- <Link :href="route('scanner')" class="w-20 h-20 m-5 mx-auto text-center ">
                                        <img src="/img/scan.png" />
                                        <img src="/img/scanner.jpg" />
                                    </Link> -->




                        </div>
                        <div>
                            <TopFiveBarChart :labels="top5Labels" :values="top5Weights" />
                        </div>

                        <div class="grid grid-cols-1 ">

                            <div class="col-span-2 mx-2 my-2">
                                <DataTable
                                  class="text-xs"
                                  :searchUrl="route('dashboard')"
                                  :items="stocks"
                                  :headers="headers"

                                />

                            </div>

                            <!-- <div class="col-span-1">

                                <PieChart
                                :top5Labels="top5Labels"
                                :top5Weights="top5Weights"

                                />
                            </div> -->

                        </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
