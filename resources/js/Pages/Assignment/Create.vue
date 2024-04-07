<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import {watch, ref,onMounted,computed} from 'vue';
import Modal from '@/Components/Modal.vue'
import debounce from 'lodash/debounce';
import axios from 'axios';
import AssignmentOrders from '@/Components/AssignmentOrders.vue';
// import AssignmentPart from '@/Components/AssignmentPart.vue';
// import swal from

import Swal from 'sweetalert2';
const props= defineProps({
    orders:Object,
    assemblers:Object,
    assignments:Object,
    station:String,
    flag:String,
})



let showModal=ref(false);

const updateAssignmentCount = (order_no, part) => {
  const index = ordersArray.value.findIndex(entry => entry.order_no === order_no);
  if (index !== -1) {
    ordersArray.value[index][`${part}_Assignment_Count`]++;
  }
};

const assignmentArray=ref([])

const assignedArray=ref(props.assignments);

const AddPart= (order_no,part)=>{
    // console.log('here')
     assignmentArray.value.push({order_no,part});
    //  assignedArray.value.push({order_no,part});

     updateAssignmentCount(order_no,part)
}




const checkAssigned = (order_no, part) => {
let index = ordersArray.value.findIndex(item =>
        item.order_no === order_no && item[`${part}_Assignment_Count`] > 0
    );
    return index !== -1;
};

const addAssignmentHandler=(assignment)=>{

        assignmentArray.value.push({ order_no: assignment.order_no, part:assignment.part,weight:assignment.weight });
        form.parts=assignmentArray.value;
        assigned.value=form.parts

    }


const totalWeight = computed(() => {
  return assignmentArray.value.reduce((sum, order) => {
    return sum + parseFloat(order.weight);
  }, 0);
});

const form=useForm({
  parts:[],
  assignee:'',
});

const assigned=ref([]);
const  makeAssignment=  ()=>{
axios.post(route('assignment.store'), {'parts':form.parts,'assignee':form.assignee,'station':props.station})
            .then(response => { //assigned.value=response.data[0].assigned
                                if (response.data[0].assigned) {
                                    assigned.value=assignmentArray.value
                                }
                                assignedArray.value=response.data[0].assignments
                                assignmentArray.value=[]

                     Swal.fire('Success!','Assignment Successful','success');
                 })
             .catch( error=>console.log(error));

  form.reset()

}



</script>

<template>
    <Head title="Assignment"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800 rounded">
                Assignment Station {{ station.toUpperCase() }}</h2>
                <p class="text-center">{{flag.toUpperCase() }}</p>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                         <div class="w-full text-center">
                             <!-- <div class="">
                                   <Button
                                      label="Download"
                                      severity="success"
                                      class="mx-2"
                                      @click="showModal=true"
                                    />

                                </div> -->

                         </div>

                            <div class="relative grid overflow-x-auto shadow-md md:grid-cols-4 sm:grid-cols-1 sm:rounded-lg">
                               <div class="col-span-3">
                                  <AssignmentOrders
                                        @add-assignment="addAssignmentHandler"
                                        :orders="orders"
                                        :assignments="assignedArray"
                                        :station="station"
                                        :assigned="assigned"
                                   />
                              </div>

                        <div class="items-center col-span-1 px-4 shadow-lg">
                            <div class="p-3 m-3 text-center text-white rounded-md bg-slate-500"> Make Assignments</div>
                           <table v-show="assignmentArray.length >0" class="text-center item-center">
                            <tr >
                                <th>Order</th>
                                <th>Part</th>
                                <th>Weight</th>
                            </tr>
                            <tr v-for="ass in assignmentArray">
                                <td class="text-center">{{ass.order_no }}</td>
                                <td class="text-center">{{ass.part }}</td>
                                <td class="text-center">{{ass.weight }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="font-bold">Total</td>
                                <td class="text-center">{{totalWeight.toFixed(1) }}</td>
                            </tr>
                           </table>
                           <div class="gap-4 m-5 text-center ">
                            <form class="flex flex-col gap-2" @submit.prevent="makeAssignment()" v-show="assignmentArray.length>0" >
                            <!-- <label >Assignee</label> -->
                            <Dropdown
                                :options="assemblers"
                                option-label="name"
                                option-value="id"
                                filter
                                placeholder="Assignee"
                               v-model="form.assignee"
                            />
                            <Button
                                label="Assign"
                                severity="success"
                                icon="pi pi-send"
                                type="submit"
                                :disabled="form.processing||form.assignee==''"
                            />
                            <!-- <input type="text" v-model="form.parts" hidden> -->

                            </form>

                           </div>

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
