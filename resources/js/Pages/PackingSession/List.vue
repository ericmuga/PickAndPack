



<script setup>
  import SearchBox from '@/Components/SearchBox.vue'
import Toolbar from 'primevue/toolbar';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { useForm } from '@inertiajs/inertia-vue3'
import {ref,computed} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'
import axios from 'axios';
import { Link } from '@inertiajs/inertia-vue3';



const props=defineProps({
    checkers:Object,
    sessions:Object,
    orders:Object,
    rows:String,
    todaysPackedTonnage:Number,
    packingTime:String,
    roles:Array,
});

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

})





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
            <div class="flex items-center justify-center gap-4 text-xl font-semibold leading-tight text-center text-indigo-400 place-items-center">
               <h2>
                 {{!adminOrSupervisor?'My':''}} Packing Sessions
               </h2>

            </div>
            <div class="grid w-full text-center lg:grid-cols-5 md:grid-cols-2 sm:grid-cols-1">
    <!-- Sales Today Card -->
    <div class="flex flex-col justify-between p-4 mx-2 my-4 bg-white rounded-md shadow-md">
      <div>
        <h2 class="mb-2 text-xl font-semibold">{{!adminOrSupervisor?'My':''}} Packing Today</h2>
        <!-- Your sales today data goes here -->
        <div class="text-3xl font-bold">{{ todaysPackedTonnage }}T</div>
        <div>

        </div>


      </div>
      <!-- <div class="mt-4 text-sm text-gray-500">+5% from yesterday</div> -->
    </div>
    <div class="flex flex-col justify-between p-4 mx-2 my-4 bg-white rounded-md shadow-md">
      <div>
        <h2 class="mb-2 text-xl font-semibold">{{!adminOrSupervisor?'My':''}} Packing Time</h2>
        <!-- Your sales today data goes here -->
        <div class="text-3xl font-bold">{{ packingTime }} Mins</div>
        <div>

        </div>


      </div>
      <!-- <div class="mt-4 text-sm text-gray-500">+5% from yesterday</div> -->
    </div>
    </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>
                                    <!-- <Button label="New" icon="pi pi-plus" class="mr-2" />
                                        <Button label="Upload" icon="pi pi-upload" class="p-button-success" /> -->
                                        <!-- <i class="mr-2 pi pi-bars p-toolbar-separator" /> -->
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="sessions" class="p-button-warning"></SplitButton> -->
                                    <Button
                                         label="New"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>
                                <template #center>
                                    <div>
                                        <Pagination :links="sessions.meta.links" />
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>


                                        <!-- <a :href="route('sessions.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="sessions"/>
                                        </a> -->




                                       <SearchBox :model="route('packingSession.index')" />
                                    </template>
                                        </Toolbar>
                                        <div v-if="sessions.data.length==0" class="w-full p-3 mt-2 text-center">
                                                 No Sessions were found.
                                                </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg" v-else>

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                            Barcode
                                                        </th> -->
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                           #
                                                        </th> -->
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

                                                    <!-- <td class="px-3 py-2 text-xs">
                                                        {{ session.id }}
                                                    </td> -->
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
                    </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <Pagination :links="sessions.meta.links" />
                            </div>
                        </template>
                    </Toolbar>


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

       <Dropdown
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
        />
        <Dropdown
          v-model="form.checker_id"
          :options="props.checkers.data"
          optionLabel="user_name"
          optionValue="id"
          filter=""
          placeholder="Select Checker"
       />



        <Button
          severity="info"
          type="submit"
          :label=mode.state
          :disabled="form.checker_id==''||form.order_no==''||form.part==''||form.processing"

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
