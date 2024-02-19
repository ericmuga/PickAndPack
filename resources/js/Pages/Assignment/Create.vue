<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import {watch, ref,onMounted,computed} from 'vue';
import Modal from '@/Components/Modal.vue'
import debounce from 'lodash/debounce';
import axios from 'axios';
import AssignmentOrders from '@/Components/AssignmentOrders.vue';
import AssignmentPart from '@/Components/AssignmentPart.vue';


const props= defineProps({
    orders:Object,
    assemblers:Object,
    assignments:Object,
})

// onMounted(()=>{
//      assignmentsArray.value = props.assignments;
// })




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

const addAssignmentHandler=(assignment)=>{

        assignmentArray.value.push({ order_no: assignment.order_no, part:assignment.part,weight:assignment.weight });
    }


const totalWeight = computed(() => {
  return assignmentArray.value.reduce((sum, order) => {
    return sum + parseFloat(order.weight);
  }, 0);
});


const form=ref({
  parts:[],
  assignee:''


});
const makeAssignment=()=>{
  form.parts=assignmentArray.value;
  form.post(route('assignments.store'))
  form.reset()
  assignmentArray.value=[];

}



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
                                  <AssignmentOrders @add-assignment="addAssignmentHandler" :orders="orders" :assignments="assignments"/>
                              </div>

                        <div class="col-span-1 px-4 shadow-lg">
                            <div class="p-3 m-3 text-center rounded-md bg-slate-500"> Make Assignments</div>
                           <table v-show="assignmentArray.length >0">
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
                                <td class="text-right">{{totalWeight }}</td>
                            </tr>
                           </table>
                           <div class="flex flex-col gap-4 m-5 text-center">
                            <form @submit.prevent="makeAssignment()" v-show="assignmentArray.length>0" >
                            <Dropdown
                                :options="assemblers"
                                option-label="name"
                                option-value="code"
                                v-model="form.assignee"
                            />
                            <Button
                                label="Assign"
                                severity="success"
                                icon="pi pi-send"
                                type="submit"
                                :disabled="form.processing||form.assignee==''"
                            />
                            <input type="text" v-model="form.parts" hidden>

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
