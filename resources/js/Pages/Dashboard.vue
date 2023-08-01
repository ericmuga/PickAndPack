<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import StatsTile from '@/Components/StatsTile.vue';
import { Link } from '@inertiajs/inertia-vue3'
import SpacedRule from '@/Components/SpacedRule.vue';
import DataTable from '@/Components/DataTable.vue';


defineProps({
               todays:Number,
               pending:Number,
               refreshError:String,
               stocks:Object,
               headers:Object,
          })

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->
                        <div v-if="refreshError!=null">
                            {{ refreshError }}
                        </div>
                        <div class="items-center justify-between w-full md:grid md:grid-cols-3 md:space-x-1 md:gap-1 sm:space-y-2 md:flex-row">

                            <Link :href="route('order.list')" :active="route().current('order.list')">

                               <StatsTile :Qty=todays tile="Todays" class="text-black bg-cyan-100" />
                            </Link>
                            <StatsTile tile="Confirmed" :Qty=todays-pending  class="text-white bg-teal-600 " />
                            <StatsTile :Qty=pending tile="Pending"  class="text-white bg-rose-700 md:mt-2" />





                         </div>

                        <!--end of stats bar-->

                    </div>

                        <div class="flex items-center w-full text-center">
                                     <SpacedRule class="text-center "/>

                                      <!-- <Link :href="route('scanner')" class="w-20 h-20 m-5 mx-auto text-center ">
                                        <img src="/img/scan.png" />
                                        <img src="/img/scanner.jpg" />
                                    </Link> -->

                                    <Link :href="route('refresh')" class="w-20 h-20 m-5 mx-auto text-center ">
                                        <img src="/img/refresh.png" />
                                        <!-- <img src="/img/scanner.jpg" /> -->
                                    </Link>


                        </div>

                        <div class="grid grid-cols-3 ">

                            <div class="col-span-2 mx-2 my-2">
                                <DataTable
                                  class="text-xs"
                                  :searchUrl="route('dashboard')"
                                  :items="stocks"
                                  :headers="headers"

                                />

                            </div>

                        </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
