<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import StatsTile from '@/Components/StatsTile.vue';
import { Link } from '@inertiajs/inertia-vue3'
import SpacedRule from '@/Components/SpacedRule.vue';

// let Todays={
//     title:"Todays",
//     number:200,

// }

// let Completed={
//     title:"Completed",
//     number:170,

// }

// let PendingExecution={
//     title:"Pending Execution",
//     number:30,

// }

defineProps({
               todays:Number,
               pending:Number,
               refreshError:String


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
                        <div class=" md:grid md:grid-cols-3 md:space-x-1 w-full items-center justify-between md:gap-1  sm:space-y-2 md:flex-row">

                            <Link :href="route('order.list')" :active="route().current('order.list')">

                               <StatsTile :Qty=todays tile="Todays" class=" bg-cyan-100 text-black" />
                            </Link>
                            <StatsTile tile="Confirmed" :Qty=todays-pending  class="bg-teal-600 text-white  " />
                            <StatsTile :Qty=pending tile="Pending"  class=" bg-rose-700 text-white md:mt-2" />





                         </div>

                        <!--end of stats bar-->

                    </div>

                        <div class="flex items-center text-center w-full">
                                     <SpacedRule class=" text-center"/>

                                      <!-- <Link :href="route('scanner')" class=" mx-auto h-20 w-20 text-center m-5">
                                        <img src="/img/scan.png" />
                                        <img src="/img/scanner.jpg" />
                                    </Link> -->

                                    <Link :href="route('refresh')" class=" mx-auto h-20 w-20 text-center m-5">
                                        <img src="/img/refresh.png" />
                                        <!-- <img src="/img/scanner.jpg" /> -->
                                    </Link>


                        </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
