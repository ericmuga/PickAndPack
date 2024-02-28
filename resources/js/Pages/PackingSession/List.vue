



<script setup>

import Toolbar from 'primevue/toolbar';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { Head } from '@inertiajs/inertia-vue3';
import { useForm } from '@inertiajs/inertia-vue3'
import {ref,computed,onMounted,watch} from 'vue';

import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'

import axios from 'axios';

import debounce from 'lodash/debounce';





const props=defineProps({
    checkers:Object,
    checker_id:String,
    orders:Object,
    rows:String,
    todaysPackedTonnage:Number,
    packingTime:String,
    roles:Array,
});
let ordersArray=ref([]);
onMounted(() => {

    if (props.checker_id!=null) form.checker_id=props.checker_id

//    const filteredOrders = props.orders.filter(order => {
//     const packingCount = parseInt(order.A_Packing_Count) +
//                            parseInt(order.B_Packing_Count) +
//                            parseInt(order.C_Packing_Count) +
//                            parseInt(order.D_Packing_Count);

//     const assemblyCount = parseInt(order.A_Assembly_Count) +
//                          parseInt(order.B_Assembly_Count) +
//                          parseInt(order.C_Assembly_Count) +
//                          parseInt(order.D_Assembly_Count);

//     return packingCount > assemblyCount;
// });

// ordersArray.value.push(...filteredOrders);
ordersArray.value=props.orders



});


let newItem=ref('');


watch( newItem,
 debounce( ()=>{
    // const filteredOrders = props.orders.filter(order => {
    //         const packingCount = parseInt(order.A_Packing_Count) +
    //                             parseInt(order.B_Packing_Count) +
    //                             parseInt(order.C_Packing_Count) +
    //                             parseInt(order.D_Packing_Count);

    //         const assemblyCount = parseInt(order.A_Assembly_Count) +
    //                             parseInt(order.B_Assembly_Count) +
    //                             parseInt(order.C_Assembly_Count) +
    //                             parseInt(order.D_Assembly_Count);

    //         return packingCount > assemblyCount;
    //     });

    // ordersArray.value.push(...filteredOrders)
    // alert('i')
    if (newItem.value!='')
     ordersArray.value=ordersArray.value.filter(item => item.order_no.endsWith(newItem.value));
    else ordersArray.value=props.orders
 })

,500);
const adminArray = ['supervisor', 'admin'];

const adminOrSupervisor = computed(() => props.roles.some(value => adminArray.includes(value)));

const getParts=(order_no)=>{
    axios.post(route('packingSession.getOrderParts'),{order_no})
         .then(response=>{
             parts.value=response.data
         })
         .catch(error=>{
           Swal.fire('Error','An Error occurred'+error.message,'error')
         })
}

let parts=ref([]);
const form= useForm({

   checker_id:'',
   order_no:'',
   part:'',
   packing_session_id:'',

})


const selectOrderPart=(order,part,session_id)=>{

    form.order_no=order;
    form.part=part;
    form.packing_session_id=session_id;
    if (!props.checker_id){
        showModal.value=true;
    }
    else
    {
        form.checker_id=props.checker_id
        form.post(route('packingSession.store'),
                    {

                        onSuccess:()=>{
                                        form.reset();
                                        // Swal.fire(`Session Created Successfully!`,'','success');
                                      }
                    }
                   )

    }
}


const createOrUpdatesession=()=>{
    if (mode.state=='Create')
          form.post(route('packingSession.store'),
                    {

                        onSuccess:()=>{
                                        form.reset();
                                        Swal.fire(`Session ${mode.state}ed Successfully!`,'','success');
                                      }
                    }
                   )
        else
     form.patch(route('packingSession.update',form.session_no),
                {
                    onSuccess:()=>{ form.reset();
                                    Swal.fire(`Session ${mode.state}ed Successfully!`,'','success');
                                }
                });
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
    form.checker_id=session.checker_id
    form.order_no=session.order_no
    form.part=session.part

}
</script>


<template>
    <Head title="My Packing Sessions"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <!-- <div class="flex items-center justify-center gap-4 text-xl font-semibold leading-tight text-center text-indigo-400 place-items-center">
               <h2>
                 {{!adminOrSupervisor?'My':''}} Packing Sessions
               </h2>

            </div> -->
 <div class="flex flex-row justify-center w-full mt-3 text-center ">
    <!-- Sales Today Card -->
    <div class="w-full font-semibold tracking-wide text-center">Packing</div>
    </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                 <template #center>
                                   <div class="flex flex-col items-center gap-2">

                                     <input type="text" v-model="newItem"  ref="inputField" placeholder="Search Order" class="justify-center max-w-sm m-2 text-center rounded-lg bg-slate-300 ">

                                     <div v-if="ordersArray.length==0" class="w-full p-3 mt-2 text-center">
                                                 No Orders were found.
                                     </div>
                                     <div class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg"
                                     style="height: 400px;" v-else>
                                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                <tr class="text-xs text-white bg-gray-700 ">
                                                    <th scope="col" class="py-2 text-center md:px-1">Order</th>
                                                    <th scope="col" class="py-2 text-center md:px-1">Details</th>
                                                    <th scope="col" class="py-2 text-center md:px-4">A</th>
                                                    <th scope="col" class="py-2 text-center md:px-4">B</th>
                                                    <th scope="col" class="py-2 text-center md:px-4">C</th>
                                                    <th scope="col" class="py-2 text-center md:px-4">D</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="order in ordersArray" :key="order.order_no" class="font-semibold text-black bg-white hover:bg-gray-300">
                                                    <td class="px-2 py-2 text-xs break-all">{{ order.order_no }}</td>
                                                    <td class="px-2 py-2 text-xs break-all">{{ order.shp_name }}</td>
                                                    <td class="p-1 px-2 py-2 text-xs text-center">
                                                        <Button v-show="order.A_Assembly_Count>0"
                                                                icon="pi pi-gift"
                                                                :severity="order.A_Packing_Count>0?'success':'danger'"
                                                                rounded
                                                               @click="selectOrderPart(order.order_no,'A',order.packing_session_id)"
                                                         />
                                                    </td>
                                                    <td>
                                                        <Button v-show="order.B_Assembly_Count>0"
                                                                icon="pi pi-gift"
                                                                :severity="order.B_Packing_Count>0?'success':'danger'"
                                                                rounded
                                                               @click="selectOrderPart(order.order_no,'B',order.packing_session_id)"
                                                         />
                                                    </td>
                                                    <td>
                                                         <Button v-show="order.B_Assembly_Count>0"
                                                                icon="pi pi-gift"
                                                                :severity="order.B_Packing_Count>0?'success':'danger'"
                                                                rounded
                                                               @click="selectOrderPart(order.order_no,'B',order.packing_session_id)"
                                                         />
                                                    </td>
                                                    <td>
                                                         <Button v-show="order.D_Assembly_Count>0"
                                                                icon="pi pi-gift"
                                                                :severity="order.D_Packing_Count>0?'success':'danger'"
                                                                rounded
                                                               @click="selectOrderPart(order.order_no,'D',order.packing_session_id)"
                                                         />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                    </div>
                                 </template>

                            </Toolbar>
                            <!-- <Toolbar> -->


                                <!-- <template #start>


                                        <Button
                                         label="New"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                    Session History
                                </template> -->
                                <!-- <template #center>
                                    <div>
                                        <Pagination :links="sessions.meta.links" />

                                    </div>


                                </template> -->

                                    <!-- <template #end>




                                            <a :href="route('packingSessions.export')" class="">
                                                <Button icon="pi pi-download" severity="primary" text raised rounded label="Download"/>
                                            </a>

                                       <SearchBox :model="route('packingSession.index')" />
                                    </template> -->
                                        <!-- </Toolbar> -->
                                        <!-- <div v-if="sessions.data.length==0" class="w-full p-3 mt-2 text-center">
                                                 No Sessions were found.
                                                </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg" v-else>

                                             <Accordion >
                                    <AccordionTab header="Session History">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">

                                                        <th scope="col" class="px-6 py-3 text-center">
                                                           Order
                                                        </th>

                                                        <th scope="col" class="px-6 py-3 text-center">
                                                           Route
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Packer
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Checker
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Start Time
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            End Time
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Pack/Print
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Actions
                                                        </th>



                                                    </tr>
                                                </thead>


                                                <tbody>
                                                    <tr v-for="session in sessions.data" :key="session.id"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">


                                                     <td class="flex flex-col items-center px-3 py-2 text-xs ">
                                                        <div>{{ session.order_no }}-{{ session.part }}</div>
                                                        <div class="p-1 text-black bg-orange-100 rounded-md">{{ session.order.shp_name }}</div>
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ session.order.sp_search_name }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ session.packer.user_name }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ session.checker?.user_name }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ session.start_time }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ session.end_time }}
                                                    </td>
                                                     <td class="px-3 py-2 text-xs font-bold" v-if="session.system_entry==1">
                                                        <Link  :href="route('packingSession.show',session.id)">
                                                            <Button
                                                            label="Pack"
                                                            severity="primary"
                                                            icon="pi pi-send"
                                                        />
                                                        </Link>
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold" v-else>
                                                        <Link  :href="route('packingSession.show',session.id)">
                                                            <Button
                                                            label="Labels"
                                                            severity="success"
                                                            icon="pi pi-print"
                                                        />
                                                        </Link>
                                                    </td>




                                                    <td v-show="session.system_entry==1">
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('packingSession.destroy',{'id':session.id})"/>
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text
                                                                        @click="showUpdateModal(session)"
                                                                      />
                                                       </div>
                                                    </td>

                                            </tr>

                            </tbody>
                        </table>

                    </AccordionTab>
                    </Accordion>
                    </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <Pagination :links="sessions.meta.links" />
                            </div>
                        </template>
                    </Toolbar> -->


                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" >

     <div class="flex flex-col p-4 rounded-sm">

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Session</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update session</div> -->

          <form  @submit.prevent="createOrUpdatesession()">

<div class="flex flex-col justify-center gap-3">

       <!-- <Dropdown
          v-model="form.order_no"
          :options="props.orders.data"
          optionLabel="orderNo"
          optionValue="order_no"
          filter=""
          placeholder="Select Order"
          @change="getParts(form.order_no)"
       />
       <Dropdown
          v-model="form.part"
          :options="parts"
          optionLabel="part"
          optionValue="part"
          placeholder="Select Order Part"
        /> -->
        <Dropdown
          v-model="form.checker_id"
          :options="props.checkers.data"
          optionLabel="user_name"
          optionValue="id"
          filter=""
          v-show="!props.checker_id"
          placeholder="Select Checker"
       />



        <Button
          severity="info"
          type="submit"
          :label=mode.state
          :disabled="form.order_no==''||form.part==''||form.processing"

        />
        <Button label="Cancel" severity="warning" icon="pi pi-cancel" @click="showModal=false"/>
</div>

    </form>

     </div>

  </Modal>
</AuthenticatedLayout>

</template>
<style lang="scss" scoped>

</style>
