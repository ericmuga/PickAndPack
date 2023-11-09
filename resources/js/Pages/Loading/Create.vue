

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { onMounted,ref } from 'vue';
import Pagination from '@/Components/Pagination.vue'

// import VesselCard from '@/Components/VesselCard.vue';



    const props = defineProps({
                        vessels:Object,
           })

    const form = useForm({

    })


</script>

<template>
 <Head title="vessels"/>

    <AuthenticatedLayout>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Load</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>


                                </template>
                                <template #center>
                                    <div>
                                        <!-- <Pagination :links="vessels.meta.links" /> -->
                                    </div>


                                </template>

                                    <template #end>



                                    </template>
                                        </Toolbar>





 <div class="grid sm:grid-cols-1 md:grid-cols-4">

       <div v-if="vessels.data.length==0" class="p-5 text-center text-teal-500" > No vessels were found</div>

       <div v-for="vessel in vessels.data" :key="vessel.id" class="items-center">

          <!-- <VesselCard :vessel="vessel" /> -->
         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        {{ vessel.id }}


       </div>

</div>
                                      </div>
                                      </div>
                                      </div>
         <Toolbar>
             <template #center>

                <!-- <Pagination :links="vessels.meta.links" /> -->
             </template>


       </Toolbar>
                                      </div>
    </div>
    </AuthenticatedLayout>
</template>



<style lang="scss" scoped>

</style>
