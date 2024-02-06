<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import {watch, ref,onMounted} from 'vue';
import Modal from '@/Components/Modal.vue'
import debounce from 'lodash/debounce';
import axios from 'axios';
import AssignmentOrders from '@/Components/AssignmentOrders.vue';


const props= defineProps({
    orders:Object,
    assemblers:Object,
})






let showModal=ref(false);

const updateAssignmentCount = (order_no, part) => {
  const index = ordersArray.value.findIndex(entry => entry.order_no === order_no);
  if (index !== -1) {
    ordersArray.value[index][`${part}_Assignment_Count`]++;
  }
};

const assignmentArray=ref([])

const assignee=ref('')

const assign=()=>{
    axios.post('assignment.store',{'assignmentArray':assignmentArray.value,'assignee':assignee.value})
         .then(()=>{
            assignee.value=''
            assignmentArray.value=[];
         })
         .catch((response)=>{
           console.log(response)
         })
}

const AddPart= (order_no,part)=>{
    // console.log('here')
     assignmentArray.value.push({order_no,part});
     updateAssignmentCount(order_no,part)
}



const checkAssigned = (order_no, part) => {
    let index = ordersArray.value.findIndex(item =>
        item.order_no === order_no && item[`${part}_Assignment_Count`] > 0
    );
    return index !== -1;
};

const addAssignmentHandler=()=>{

}


const form=ref({
    start_date:null,
    end_date:null,

});




</script>

<template>
    <Head title="Assignment"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800 rounded"> Pending Assignment</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <div class="relative grid grid-cols-4 overflow-x-auto shadow-md sm:rounded-lg">
                               <div class="col-span-3">
                                  <AssignmentOrders @add-assignment="addAssignmentHandler()" :orders="orders"/>
                              </div>

                        <div class="col-span-1">
                          {{ assignmentArray }}
                        </div>
                    </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <!-- <Pagination :links="orders." /> -->
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
    <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Select Date Range</div>
    <form class="flex flex-col items-center w-full gap-2 p-5" @submit.prevent="postForm()">
      <div class="flex flex-row items-center gap-1 ">
        <label>Start Date</label>
        <input type="date" class="p-2 rounded-md"
            v-model="form.start_date"
        />
      </div>
      <div class="flex flex-row items-center gap-1">
        <label>Start Date</label>
        <input type="date" class="p-2 rounded-md"
            v-model="form.end_date"
        />
      </div>
      <div class="flex flex-row items-center gap-1">
       <Button label="Cancel" severity="danger" @click="showModal=false" icon="pi pi-cancel" type="reset"/>

       <a
        :href="route('registry.download')+'?'+'start_date='+form.start_date+'&end_date='+form.end_date"
         >
       <Button
         severity="success" icon="pi pi-send"
         class="w-full"
         @click="showModal=false"
         label="Download"
         />


       </a>



      </div>

    </form>
  </Modal>
</AuthenticatedLayout>

</template>

<style>
button:hover {
    cursor: pointer;
}

p:hover {
    cursor: pointer;
}



</style>
