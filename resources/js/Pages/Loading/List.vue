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
import axios from 'axios';
import moment from 'moment';
import pdfMake from 'pdfmake/build/pdfmake';
// import * as pdfFonts from 'pdfmake/build/vfs_fonts';

// import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "@/Composables/pdfFonts";
pdfMake.vfs = pdfFonts;
const currentDate = moment().format('DD/MM/YYYY'); // Format the current date


const showContents = (session) => {

    //get lines that should be loaded for the route and check status

//   console.log(lines);
  let orders = '';
  let lines=session.lines;

  for (let index = 0; index < lines.length; index++) {

    orders += '<tr>'+
                 '<td class="p-1 text-left">' +lines[index].vessel +'</td>'+
                 '<td class="p-1 text-left">' +lines[index].vessel_qr +'</td>'+
                 '<td class="p-1 text-left">' +lines[index].shp_name+'</td>'+
                '<td class="p-1 text-center">';
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
    //    console.log(lines)

       axios.post(route('loadingSheet'),{'id':session.id})
            .then(response=>{
                // exportData(response.data,null);
                // console.log(response.data)
              generatePDF(response.data);
             });

        Swal.close();
      });
    },
  });
};

const generatePDF = async (loadingData)=> {
      const dataArray = loadingData.lines;
      console.log(dataArray);

// Group the data by "order_no" and "shp_name"
// Group the data by "order_no", "shp_name", and category
const groupedData = Object.values(dataArray.reduce((accumulator, currentItem) => {
  const key = currentItem.order_no + '_' + currentItem.shp_name;

  if (!accumulator[key]) {
    accumulator[key] = {
      order_no: currentItem.order_no,
      shp_name: currentItem.shp_name,
      A_Crates: 0,
      A_Cartons: 0,
      B_Crates: 0,
      B_Cartons: 0,
      C_Crates: 0,
      C_Cartons: 0,
      D_Crates: 0,
      D_Cartons: 0,
    };
  }

  const category = currentItem.vessel_qr.includes('_A_') ? 'A' :
                   currentItem.vessel_qr.includes('_B_') ? 'B' :
                   currentItem.vessel_qr.includes('_C_') ? 'C' :
                   currentItem.vessel_qr.includes('_D_') ? 'D' : 'Unknown';

  if (currentItem.vessel=='Crate') accumulator[key][`${category}_Crates`] += 1; // Counting occurrences
   if (currentItem.vessel=='Carton') accumulator[key][`${category}_Cartons`] += 1; // Counting occurrences

  return accumulator;
}, {}));

// Create content for the pdfmake document definition
const tableBody = groupedData.map(item => [
  item.order_no,
  item.shp_name,
  item.A_Crates,
  item.A_Cartons,
  item.B_Crates,
  item.B_Cartons,
  item.C_Crates,
  item.C_Cartons,
  item.D_Crates,
  item.D_Cartons,
]);

const tableBody2 =loadingData.expected_load.map(item=>[item.order_no,item.shp_name,
  0,
  0,
  0,
  0,
  0,
  0,
  0,
  0,]);

console.log(tableBody)
console.log(tableBody2)
const docDefinition = {
  content: [
        {
        text: 'Loading Sheet',
        style: 'header',
        alignment: 'center',

       },


       {
      columns: [
        {
          stack: [
            {
              text: `Route: ${loadingData.sp[0].Name}`,
              style: 'header',
              alignment: 'left',
            },
            {
              text: `Shipment Date: ${loadingData.shp_date}`,
              style: 'header',
              alignment: 'left',
            },
            {
              text: `Vehicle: ${loadingData.vehicle}`,
              style: 'header',
              alignment: 'left',
            },
          ],
        },
        {
          stack: [
            {
              text: `Loader: ${loadingData.loader}`,
              style: 'header',
              alignment: 'right',
            },
            {
              text: `Loading Date: ${loadingData.loading_date}`,
              style: 'header',
              alignment: 'right',
            },
            {
              text: `Status: ${loadingData.status}`,
              style: 'header',
              alignment: 'right',
            },
          ],
        },
      ],
    },
       {

      table: {
        headerRows: 1,
        body: [
          ['Order No.', 'Shp Name', 'A (Crt)', 'A (Ctn)', 'B (Crt)', 'B (Ctn)', 'C (Crt)', 'C (Ctn)', 'D (Crt)', 'D (Ctn)'],
          ...tableBody,
          ...tableBody2
        ]
      }
    }
  ],
  styles: {
                header: {
                    fontSize: 12,
                    bold: true,
                    margin: [0, 0, 0, 10],
                },
                totalRow: {
                    fontSize: 14,
                    bold: true,
                },
            },
};

// Create and download the PDF
pdfMake.createPdf(docDefinition).download('grouped_data_table.pdf');

}
const formatNumber=(number)=> {
    return new Intl.NumberFormat('en-US').format(number);
}









const { exportToExcel } = useExcel();
const fileName = 'LoadingSheet';

const exportData = (jsonData, columns) => {
 exportToExcel(jsonData, columns, fileName);

};
const sessionsArray =ref([]);
onMounted(()=>{
    sessionsArray.value=props.sessions;
    inputField.value.focus();
})



const props=defineProps({
    sessions:Object,
    drivers:Object,
    vehicles:Object,
    loaders:Object,
    spcodes:Object,
})
    const inputField=ref(null);



const filteredView=(id)=>{

Inertia.get(route('loadSession')+'?id='+id)
}



const form= useForm({
     'vehicle_id':'',
     'loader_id':'',
     'driver_id':'',
     'assistant_driver_id':'',
     'sp_code':'',
     'shp_date':''


})





const createOrUpdateItem=()=>{

    let data={'vehicle_id':form.vehicle_id,
              'loader_id':form.loader_id,
              'driver_id':form.driver_id,
              'assistant_driver_id':form.assistant_driver_id,
              'sp_code':form.sp_code,
              'shp_date':form.shp_date
             }

    if (mode.state=='Create')
         axios.post(route('loadingSession.store'),data)
              .then(response=>{
                sessionsArray.value=response.data.sessions;
              })
              .catch(error=>{Swal.fire('Error!',error.message,'error')})

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

const showUpdateModal=(id)=>{

    let session=sessionsArray.value.filter(s=>s.loading_session_id==id)[0]
    // console.log(session)
    mode.state='Update'
    form.vehicle_id= parseInt(session.vehicle_id)
    form.driver_id=session.driver_id
    form.assistant_driver_id=session.assistant_driver_id
    form.sp_code=session.sp_code
    form.shp_date=session.shp_date
    showModal.value=true
}

const shp_date=ref();

const search=ref()
watch(search, debounce(()=>{

    if (sessionsArray.value.length>0)
    {    if (search.value!='')
            {
            sessionsArray.value=sessionsArray.value.filter(session=>
                {
                    return session.vehicle_plate.startsWith(search.value.toUpperCase())
                    ||session.sp_name.includes(search.value)

                })
            }

        else
            sessionsArray.value=props.sessions;

        if(shp_date.value) sessionsArray.value=sessionsArray.value.filter(session=>session.shp_date==shp_date.value)

    }
    }, 500));



watch(shp_date,()=>{
    if (!shp_date.value)
       sessionsArray.value=props.sessions;
    else
      sessionsArray.value=sessionsArray.value.filter(session=>session.shp_date==shp_date.value)

   if (search.value!==undefined)
    {
      sessionsArray.value=sessionsArray.value.filter(session=>
        {
            return session.vehicle_plate.startsWith(search.value.toUpperCase())
            ||session.sp_name.includes(search.value)

        })
    }

});








</script>


<template>
    <Head title="Loading"/>

    <AuthenticatedLayout @add="showModal=true">
          <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800">Loading Sessions</h2>
        </template>

        <div class="items-center py-6 text-center" >
            <!-- <Modal :show="true" > Hi there </Modal> -->
            <div class="items-center mx-auto text-center max-w-7xl sm:px-6 lg:px-8" >
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <!-- <div> -->
                            <Toolbar>
                                <template #start>
                                      <Button
                                         label="Add Session"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>

                                <template #center>
                                    <div>
                                        <!-- <Pagination :links="orderLines.meta.links" /> -->
                                        <input type="text" v-model="search"  ref="inputField" class="m-2 rounded-lg bg-slate-300 text-md">
                                    </div>
                                    <div class="flex flex-row">
                                    <input type="date"
                                        v-model="shp_date"
                                        />
                                        <Button v-show="shp_date!=null" @click="shp_date=null" icon="pi pi-times" severity="danger" outlined aria-label="Cancel" />

                                     </div>
                                </template>

                              </Toolbar>

                                    <div class="grid items-center grid-cols-1 overflow-x-auto overflow-y-auto text-center shadow-md" style="height: 400px;">

<!---table comes here-->
                                        <table class="-mt-40 text-sm text-gray-500 dark:text-gray-400" >
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                <tr class="bg-slate-300">
                                                    <!-- <th scope="col" class="px-2 py-1">
                                                        Barcode
                                                    </th> -->
                                                    <th >
                                                        Session Id
                                                    </th>
                                                    <th class="text-center" >
                                                        Route
                                                    </th>
                                                        <th class="text-center" >
                                                        Shipment Date
                                                    </th>
                                                    <!--
                                                    <th scope="col" class="px-2 py-1">
                                                        Assistant Loader
                                                    </th>-->
                                                    <th >
                                                        Vehicle
                                                    </th>

                                                        <!-- <th scope="col" class="px-2 py-1">
                                                        Driver
                                                    </th> -->
                                                    <th >
                                                        Load
                                                    </th>

                                                    <!-- <th col="2">
                                                        Actions
                                                    </th> -->



                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="session in sessionsArray" :key="session.loading_session_id"
                                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                <td class="">
                                                    {{ session.loading_session_id }}
                                                </td>

                                                    <td class="">
                                                    {{ session.sp_name }}
                                                </td>
                                                <td class="">
                                                    {{ session.shp_date}}
                                                </td>

                                                    <td class="">

                                                        {{ session.vehicle_plate }}

                                                </td>

                                                    <!-- <td class="px-3 py-2 text-xs">

                                                        {{session.driver}}

                                                </td> -->

                                                <td class="">



                                                        <!-- <Button
                                                            v-if="session.lines.length>0"
                                                            type="button"
                                                            label="Load"
                                                            icon="pi pi-gift"
                                                            :badge="session.lines.length"
                                                            badgeClass="p-badge-danger"
                                                            outlined
                                                            @click="showContents(session)"
                                                            /> -->



                                                </td>


                                                <td class="px-3 py-2 text-xs text-center">
                                                    <Button
                                                        @click="filteredView(session.loading_session_id)"
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


                                                                    @click="showUpdateModal(session.loading_session_id)"
                                                                    />
                                                    </div>
                                                </td>

                                        </tr>

                        </tbody>
                                        </table>
                                  </div>






                <!-- </div> -->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" >

     <div class="flex flex-col p-4 rounded-sm">

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Loading Session</div>
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
