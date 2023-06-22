<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Modal from '@/Components/Modal.vue'
// import Button from 'primevue/button';
import { useForm } from '@inertiajs/inertia-vue3';

import {watch, ref} from 'vue';
// import Route from 'vendor/tightenco/ziggy/src/js/Route';


const props= defineProps({
   prepackLines:Object,
   prepackItems:Object
})


let showModal=ref(false);

const form= useForm({
       'batch_no':'',
       'order_no':'',
       'item_no':'',
       'sector':'',
       'sp_code':'',
       'shp_date':'',

})

const submitForm=()=>{
   //download
   form.post(route('orders.downloadPrepacks'))
}

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
                                        <!-- <Button label="New" icon="pi pi-plus" class="mr-2"  @click="showModal=true" /> -->
                                        <!-- <Button label="Download" icon="pi pi-download" class="p-button-success"
                                         @click="showModal=true"
                                        /> -->
                                        <!-- <i class="mr-2 pi pi-bars p-toolbar-separator" /> -->
                                    <!-- <SplitButton label="Save" icon="pi pi-check" :model="items" class="p-button-warning"></SplitButton> -->
                                </template>
                                <template #center>
                                    <Pagination :links="prepackLines.meta.links" />
                                </template>
                                <template #end>
                                    <Button label="Download Prepacks" icon="pi pi-download" class="p-button-success"
                                         @click="showModal=true"
                                        />
                                    <!-- <SearchBox model="prepackLines.index" /> -->

                                </template>






                               </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <!-- <th scope="col" class="px-4 py-3">
                                                            Barcode
                                                        </th> -->
                                                        <!-- <th scope="col" class="px-4 py-3">
                                                            Batch No.
                                                        </th> -->

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

                                                    <!-- <td class="px-3 py-2 text-xs font-thin">
                                                        {{ item.batch_no}}
                                                    </td> -->

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

     <div class="w-full p-4 font-bold text-center text-white bg-slate-600"> Filters</div>
       <div >


        <form @submit.prevent="submitForm()" class="flex flex-col justify-center gap-2 p-5">
            <!-- <Dropdown v-model="form.part" :options="parts" optionLabel="name" editable="" optionValue="code" placeholder="Select Part" class="" /> -->
            <!-- <Dropdown v-model="form.sector" :options="sectors" optionLabel="name" editable="" optionValue="code" placeholder="Select Sector"  /> -->
            <InputText v-model="form.batch_no" placeholder=" Batch No"></InputText>
            <!-- <InputText v-model="form.item_no" placeholder=" Batch No"></InputText> -->
            <Dropdown v-model="form.item_no" :options="props.prepackItems" optionLabel="description" editable="" optionValue="item_no" placeholder="Prepack Item" class="" />
            <InputText v-model="form.sector" placeholder=" Sector"></InputText>
            <InputText v-model="form.order_no" placeholder=" Order No"></InputText>
            <InputText v-model="form.sp_code" placeholder="Salesperson Code"/>
            <label>Shipment Date</label>
            <input v-model="form.shp_date" placeholder="Shipment Date" type="date"/>
            <Button  label="Download" severity="primary"  type="submit" :disabled="form.processing" />

        </form>


     </div>


  </Modal>

</template>
