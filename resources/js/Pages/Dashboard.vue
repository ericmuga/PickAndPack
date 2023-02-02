<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import StatsTile from '@/Components/StatsTile.vue';
import { Link } from '@inertiajs/inertia-vue3'
import SpacedRule from '@/Components/SpacedRule.vue';

let Todays={
    title:"Todays",
    number:200,

}

let Completed={
    title:"Completed",
    number:170,

}

let PendingExecution={
    title:"Pending Execution",
    number:30,

}
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
                        <div class=" md:grid md:grid-cols-3 md:space-x-1 w-full items-center justify-between md:gap-1  sm:space-y-2 md:flex-row">

                            <Link :href="route('total')" :active="route().current('total')">

                               <StatsTile :tile=Todays class="bg-rose-700 text-white md:mt-2 " />
                            </Link>

                            <StatsTile :tile=PendingExecution  class="bg-cyan-100 text-black " />
                            <StatsTile :tile=Completed  class="bg-emerald-600 text-white  " />




                         </div>

                        <!--end of stats bar-->

                    </div>

                        <div class="flex items-center text-center w-full">
                                     <SpacedRule class=" text-center"/>

                                      <Link :href="route('scanner')" class=" mx-auto h-20 w-20 text-center m-5">
                                        <img src="/img/scan.png" />
                                        <!-- <img src="/img/scanner.jpg" /> -->
                                    </Link>


                        </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
