<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Modal from '@/Components/Modal.vue'
import {ref,onMounted} from 'vue';
import{pickBy} from 'lodash'
import useFileDownload from '@/Composables/useFileDownload.js'
import moment from 'moment';
// import useDistinctValues from '@/Composables/useDistinctValues.js'
import { useSubObjectExtractor } from '@/Composables/useSubObjectExtractor';


const props= defineProps({
   prepackLines:Object,
   prepackItems:Object,
   prepackBatches:Object,
   orders:Object,
   sp_codes:Object,
})

// let batches= props.prepackBatches.forEach(item => {
//                 const formattedDate = moment(item.created_at).format('MMMM Do YYYY, h:mm:ss a');
//                 item.created_at = formattedDate;
//                 });
const form= ref({
       'batch_no':'',
       'order_no':'',
       'item_no':'',
       'sector':'',
       'sp_code':'',
       'shp_date':'',

})
const { downloadFile } = useFileDownload();






const submitForm=()=> { downloadFile(pickBy(form.value, value => value !== null && value !== ''),
                                       route('linePrepacks.download')
                                     );
                        showModal.value=false;
                      }
let showModal=ref(false);



</script>

<template>
    <Head title="Prepacks Lines"/>

    <AuthenticatedLayout @add="showModal=true">

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
                                    <Pagination :links="prepackLines.meta.links" />
                                </template>
                                <template #end>
                                    <div class="mr-2">
                                         <Button icon="pi pi-download"
                                         @click="showModal=true;
                                         " severity="success"
                                         label="download"
                                          aria-label="Favorite" />
                                    </div>

                                    <SearchBox model="linePrepacks.index" />

                                </template>
                            </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <!-- <th scope="col" class="px-4 py-3">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-4 py-3">
                                                            Batch No.
                                                        </th>

                                                        <th scope="col" class="px-4 py-2">
                                                            Order No.
                                                        </th>
                                                        <!-- <th scope="col" class="px-4 py-3 ">
                                                            Part No
                                                        </th> -->
                                                        <!-- <th scope="col" class="px-4 py-3 ">
                                                            Customer Name
                                                        </th> -->
                                                        <th scope="col" class="px-4 py-2 ">
                                                            Customer/Ship-to Name
                                                        </th>

                                                       <!-- <th scope="col" class="px-4 py-2">
                                                            Item No.
                                                        </th> -->

                                                        <th scope="col" class="px-4 py-2">
                                                            Item Description
                                                        </th>
                                                        <th scope="col" class="px-4 py-2">
                                                            Prepacks
                                                        </th>

                                                        <th scope="col" class="px-4 py-2">
                                                            PCS
                                                        </th>
                                                         <th scope="col" class="px-4 py-2">
                                                            Carton No.
                                                        </th>

                                                        <th scope="col" class="px-4 py-2">
                                                            Prepacked By
                                                        </th>
                                                        <th scope="col" class="px-4 py-2">
                                                            Prepack Time
                                                        </th>




                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="item in prepackLines.data" :key="item.id"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs font-thin">
                                                        {{ item.batch_no}}
                                                    </td>

                                                    <td class="px-3 py-1 text-xs">
                                                        {{ item.order_no}}
                                                    </td>
<!--
                                                    <td class="px-3 py-2 text-xs">
                                                        {{ item.part}}
                                                    </td> -->
                                                <div v-if="item.order.shp_name!='' ">
                                                        <td class="px-3 py-1 text-xs" >
                                                            {{ item.order.shp_name}}
                                                        </td>
                                                    </div>
                                                    <div v-else>
                                                        <td  class="px-3 py-1 text-xs">
                                                         {{ item.order.customer_name}}
                                                        </td>
                                                    </div>

                                                    <td class="px-3 py-1 text-xs font-bold">
                                                        {{ item.line.item_description}}
                                                    </td>
                                                     <td class="px-3 py-1 text-xs font-bold text-center">
                                                        {{ item.prepack_count}}
                                                    </td>
                                                     <td class="px-3 py-1 text-xs font-bold text-center">
                                                        {{ item.total_quantity}}
                                                    </td>
                                                    <td class="px-3 py-1 text-xs font-bold text-center">
                                                        {{ item.carton_no}}
                                                    </td>

                                                     <td class="px-3 py-1 text-xs font-bold">
                                                        {{ item.user.user_name}}
                                                    </td>

                                                     <td class="px-3 py-1 text-xs font-bold">
                                                        {{ item.prepack_time}}
                                                    </td>





                                            </tr>

                            </tbody>
                        </table>
                    </div>
<toolbar>
    <template #center>
      <Pagination :links="prepackLines.meta.links" />
    </template>
</toolbar>
                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

</AuthenticatedLayout>

<Modal :show="showModal" @close="showModal=false" :errors="errors">
     <!-- {{ dynamicModalContent  }} -->

     <div class="p-4 font-bold text-center text-white bg-slate-600"> Filters</div>
       <div >


        <form @submit.prevent="submitForm()"

        class="flex flex-col justify-center gap-2 p-5">
          <MultiSelect v-model="form.batch_no" :options="props.prepackBatches"
                        optionLabel="created_at"
                        optionValue="batch_no"
                        placeholder="Select Batch Times"
                        filter
            :maxSelectedLabels="3" class="w-full md:w-20rem" />

            <MultiSelect v-model="form.item_no" :options="props.prepackItems"
                        optionLabel="description" optionValue="item_no"
                        placeholder="Prepack Item"
                        filter
            :maxSelectedLabels="3" class="w-full md:w-20rem" />

            <MultiSelect v-model="form.order_no" :options="props.orders.data"
                        optionLabel="search_name" optionValue="order_no"
                        placeholder="Select Orders"
                        filter

            :maxSelectedLabels="3" class="w-full md:w-20rem" />

            <!-- <InputText v-model="form.sector" placeholder=" Sector"></InputText> -->
            <!-- <InputText v-model="form.order_no" placeholder=" Order No"></InputText> -->
            <MultiSelect v-model="form.sp_code" :options="props.sp_codes.data"
                        optionLabel="sp_search_name" optionValue="sp_code"
                        placeholder="Select Salesperson"
                        filter

            :maxSelectedLabels="3" class="w-full md:w-20rem" />
            <div class="flex flex-row w-full space-x-4">
                <div class="w-1/2">
                    <label >Shipment Date</label>
                </div>
            <Calendar v-model="form.shp_date" selectionMode="range" :manualInput="false"  class="w-1/2"/>
            </div>

            <!-- <input v-model="form.shp_date" placeholder="Shipment Date" type="date"/> -->
            <Button  label="Download" icon="pi pi-download" class="w-sm" severity="primary"  type="submit" :disabled="form.processing" />
            <Button label="Cancel" severity="danger" icon="pi pi-cancel" @click="showModal=false"/>


        </form>


     </div>


  </Modal>

</template>
