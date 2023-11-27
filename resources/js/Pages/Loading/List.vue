<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { useForm } from '@inertiajs/inertia-vue3'
import Modal from '@/Components/Modal.vue'
import Swal from 'sweetalert2';
import useExcel from '@/Composables/useExcel.js';
const { exportToExcel } = useExcel();
const fileName = 'LoadingSheet';
const exportData = (jsonData, columns) => {
 exportToExcel(jsonData, columns, fileName);

};

const search=ref()
watch(search, debounce(()=>{Inertia.post('/order/all',{search:search.value}, {preserveScroll: true})}, 500));

const prop=defineProps({
    sessions:Object,
    drivers:Object,
    vehicles:Object,
    loaders:Object,
    spcodes:Object,
})
    const inputField=ref(null);

    onMounted(() => {
    inputField.value.focus();
});

const filteredView=(id)=>{

Inertia.get(route('loadSession')+'?id='+id)
}

//   let showModal=ref(false);
let newItem=ref('');

watch( newItem,
debounce( ()=>{Inertia.get(route('packing.index'),{'search':newItem.value})})
,500);



const form= useForm({
     'vehicle_id':'',
     'loader_id':'',
     'driver_id':'',
     'assistant_driver_id':'',
     'sp_code':'',
     'shp_date':''


})





const createOrUpdateItem=()=>{
    if (mode.state=='Create')
          form.post(route('loadingSession.store'),
                    { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`Session ${mode.state}d Successfully!`,'','success');
                                    }
                    }
                   )
        else
     form.patch(route('loadingSession.update',form.name),
                { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`Session ${mode.state}d Successfully!`,'','success');
                                    }
                    })
      showModal.value=false;


}



let mode= { state: 'Create' };


  let showModal=ref(false);


const showCreateModal=()=>{

    mode.state='Create'
    form.reset();
    showModal.value=true

}

const showUpdateModal=(session)=>{

    mode.state='Update'
    form.vehicle=session.vehicle_id
    form.assistant_loader_id=session.assistant_loader_id
    form.driver_id=session.driver_id
    form.assistant_driver_id=session.assistant_driver_id
    form.sp_code=session.sp_code
    form.shp_date=session.shp_date

    showModal.value=true
}

const showContents = (lines) => {

    //get lines that should be loaded for the route and check status


  let orders = '';

  for (let index = 0; index < lines.length; index++) {
    orders += '<tr><td class="p-2 text-left">' + lines[index].vessel + '</td><td class="p-2 text-center">' + lines[index].vessel_no + '</td><td class="p-2 text-left">';
  }

  const modalContent = `
    <div id="pdf-modal" class="p-4 bg-gray-100 rounded-lg">
      <table class="p-1 text-sm font-semibold">  ${orders}</table>
    </div>
    <button id="excel-button" class="swal2-confirm swal2-styled" style="float: right;">Excel</button>
  `;

  Swal.fire({
    title: 'Load',
    html: modalContent,
    showConfirmButton: false,
    showCancelButton: false,
    didRender: () => {
      document.getElementById('excel-button').addEventListener('click', () => {

        exportData(lines, null);
        Swal.close();
      });
    },
  });
};








</script>


<template>
    <Head title="Loading"/>

    <AuthenticatedLayout @add="showModal=true">
          <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Loading</h2>
        </template>

        <div class="py-6">
            <!-- <Modal :show="true" > Hi there </Modal> -->
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>
                                      <Button
                                         label="Add"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>

                                <template #center>
                                    <div>
                                        <!-- <Pagination :links="orderLines.meta.links" /> -->
                                        <input type="text" v-model="newItem"  ref="inputField" class="m-2 rounded-lg bg-slate-300 text-md">

                                        <!-- <SearchBox :model="route('order.pack')" /> -->
                                    <div>
                                        <!-- <Pagination :links="orders.meta.links" /> -->
                                    </div>
                                    </div>


                                </template>

                                    <template #end>




                                            <!-- <InputText v-model="search" aria-placeholder="search"/> -->

                                             <div>

                                            </div>
                                            </template>
                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

<!---table comes here-->
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <!-- <th scope="col" class="px-2 py-1">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-2 py-1">
                                                           Session Id
                                                        </th>
                                                        <th scope="col" class="px-2 py-1 text-center">
                                                            Route
                                                        </th>
                                                        <!--
                                                        <th scope="col" class="px-2 py-1">
                                                            Assistant Loader
                                                        </th>-->
                                                        <th scope="col" class="px-2 py-1">
                                                            Vehicle
                                                        </th>

                                                         <th scope="col" class="px-2 py-1">
                                                            Driver
                                                        </th>
                                                        <th scope="col" class="px-2 py-1">
                                                            Load
                                                        </th>

                                                        <th scope="col" class="px-2 py-1">
                                                           Actions
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="session in sessions.data" :key="session.id"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ session.id }}
                                                    </td>

                                                     <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ session.route }}
                                                    </td>

                                                     <td class="px-3 py-2 text-xs">

                                                            {{ session.vehicle }}

                                                    </td>

                                                     <td class="px-3 py-2 text-xs">

                                                            {{session.driver}}

                                                    </td>

                                                    <td class="px-3 py-2 text-xs">



                                                            <Button
                                                                v-if="session.lines.length>0"
                                                                type="button"
                                                                label="Load"
                                                                icon="pi pi-gift"
                                                                :badge="session.lines.length"
                                                                badgeClass="p-badge-danger"
                                                                outlined
                                                                @click="showContents(session.lines)"
                                                                />



                                                    </td>


                                                    <td class="px-3 py-2 text-xs text-center">
                                                        <Button
                                                         @click="filteredView(session.id)"
                                                         :disabled="(session.status==='complete')"
                                                         :severity="(session.status==='complete')?'success':'warning'"

                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                                            </svg>
                                                             <!-- <div class="card">
                                                                <ProgressBar :value="50"></ProgressBar>
                                                            </div> -->
                                                        </Button>


                                                    </td>
                                                    <td>
                                                       <div class="flex flex-row">
                                                          <!-- <Drop  :drop-route="route('sessions.destroy',{'session':session.id})"/> -->
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text


                                                                      @click="showUpdateModal(session.id)"
                                                                      />
                                                       </div>
                                                    </td>

                                            </tr>

                            </tbody>
                        </table>


                                        </div>






                </div>
                        <div class="items-center w-full text-center">

                            <Pagination :links="sessions.meta.links" />
                        </div>



                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" >

     <div class="flex flex-col p-4 rounded-sm">

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Item</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update Item</div> -->

          <form  @submit.prevent="createOrUpdateItem()">

<div class="flex flex-col justify-center gap-3">

     <div class="flex items-center justify-center p-1 space-x-2 ">
     <span>Shipment Date</span>
     <input type="date" v-model="form.shp_date" class="p-1 rounded-lg" />
     </div>

    <Dropdown
          :options="vehicles"
          v-model="form.vehicle_id"
          optionValue="id"
          optionLabel="plate"
          placeholder="Vehicle"
          filter
        />


        <Dropdown
          :options="spcodes"
          optionValue="code"
          v-model="form.sp_code"
          optionLabel="name"
          placeholder="Route"
          filter
        />


       <Dropdown
          :options="loaders"
          optionValue="id"
          v-model="form.assistant_loader_id"
          optionLabel="name"
          placeholder="Assistant Loader"
          filter
        />


        <Dropdown
          :options="drivers"
          v-model="form.driver_id"
          optionValue="id"
          optionLabel="name"
          placeholder="Driver"
          filter
        />

        <Dropdown
          :options="drivers"
          v-model="form.assistant_driver_id"
          optionValue="id"
          optionLabel="name"
          placeholder="Assistant Driver"
          filter
        />


        <Button
          severity="info"
          type="submit"
          :label=mode.state
          :disabled="form.processing"

        />
        <Button label="Cancel" severity="warning" icon="pi pi-cancel" @click="showModal=false"/>
</div>

    </form>

     </div>

  </Modal>
</AuthenticatedLayout>
</template>
