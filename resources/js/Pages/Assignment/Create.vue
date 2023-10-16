



<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
// import {  } from '@inertiajs/inertia-vue3'

import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted,computed} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'
// import { router } from '@inertiajs/vue3'

// import {Inertia} from '@inertiajs/inertia'

const searchKey=ref('');


const today = new Date();
let showFilters=ref(true);

const assignable = computed(() => (selectedOrderParts.value.length>0) && (assignee.value!='') );

// Add one day to it
const tomorrow = new Date(today);

tomorrow.setDate(today.getDate() + 1);

const refreshSearch=()=>{


  Inertia.get(route('assignment.create'),
                      {
                        'spcodes':selected_spcodes.value,
                       'shp_date':shipmentDate.value,
                       'selectedParts': selectedOrderParts.value,
                       'assignee': assignee.value,
                       'records':records.value
                      },
                      {

                        preserveScroll:true,preserveState:true,replace:true}
               );
}


const assign=()=>{

Inertia.post(route('assignment.store'),
             { 'selectedParts': selectedOrderParts.value,
               'assignee': assignee.value
             },
             {
                  onSuccess:()=>{selectedOrderParts.value=[]; assignee.value='';  Swal.fire('Assignment created Successfully!','','success')},
                 preserveScroll:true,
                  preserveState:true,
                        replace:true
             }
             );



}





const parts=['A','B','C','D'];


onMounted({

  // selectedOrderParts=props.orderParts
  // selected_spcodes=props.sp
  // assignee=props.assignee
});

const props=defineProps({
         order_parts:Object,
         assemblers:Object,
         ass:Object,
         spcodes:Object,
         orders:Object,
         assignee:'',
         orderParts:Object,
         shp_date:'',
         sp:Object,

  })


let filterPart=ref({})

let assignee=ref('');

const createOrUpdateItem=()=>{

    if (mode.state=='Create')
          form.post(route('assignment.store'))
        else
     form.patch(route('assignment.update',form.item_no))
      showModal.value=false;
    Swal.fire(`Assignment ${mode.state}ed Successfully!`,'','success');

}


let mode= { state: 'Create' };


  let showModal=ref(false);


const showCreateModal=()=>{

    mode.state='Create'
    form.reset()
    totalOrderQty.value=0;
    totalLines.value=0;
    showModal.value=true

}




const showUpdateModal=(item)=>{

    mode.state='Update'
    // alert(mode.state)

    form.assignee=item.assignee
    form.orders_parts=item.order_parts

    showModal.value=true
}



const sumOrderQtyByOrderPart=(dataArray, searchOrderPart)=> {
  let totalOrderQty = 0;

  for (const item of dataArray) {
    if (item.order_part === searchOrderPart) {
      totalOrderQty += parseFloat(item.order_qty);
    }
  }

  return totalOrderQty;
}

const sumOrderLineCountByOrderPart=(dataArray, searchOrderPart)=> {
  let totalOrderQty = 0;

  for (const item of dataArray) {
    if (item.order_part === searchOrderPart) {
      totalOrderQty += parseFloat(item.line_count);
    }
  }

  return totalOrderQty;
}

let totalOrderQty=ref(0);
let totalLines=ref(0);

const sumLines=()=>{ totalOrderQty.value=0; totalLines.value=0
                      for (var i = form.order_parts.length - 1; i >= 0; i--)
                       {
                         totalOrderQty.value+=sumOrderQtyByOrderPart(props.order_parts,form.order_parts[i])
                         totalLines.value+=sumOrderLineCountByOrderPart(props.order_parts,form.order_parts[i]);
                       }

                  };



let selected_spcodes=ref([]);


let filteredLines=ref([])

const showLines=(order_no,part)=>{
    //show modal for the part and display the lines
    // console.log(part);
    filteredLines.value=filterOrderAndLines(order_no,part)
   showModal.value=true;
}




function filterOrderAndLines(orderNo, part) {
  const order = props.orders.data.find((item) => item.order_no === orderNo);

  if (!order) {
    return null; // Order with the specified order_no not found
  }
  // console.log(order);
  const filteredLines = order.lines.filter((line) => line.part === part);
  // console.log(filteredLines)
  return filteredLines
}

const selectedOrderParts=ref([]);

function pushSelectedPart(newObj) {
    //console.log(newObj)
  const isDuplicate = selectedOrderParts.value.some((obj) => {
    // Check if an object with the same properties exists
    return obj.order_no === newObj.order_no && obj.part === newObj.part;
  });

  if (!isDuplicate) {
    selectedOrderParts.value.push({'order_no':newObj.order_no,'part':newObj.part});
  }


  showModal.value=false
}






function checkSelected(newObj){
   const selected = selectedOrderParts.value.some((obj) => {
    // Check if an object with the same properties exists
    return obj.order_no === newObj.order_no && obj.part === newObj.part;
  });
   return selected;
}



let shipmentDate=ref();

let records=ref([]);


const removePart=(newObj)=>
{

   let searchCriteria=newObj;

   selectedOrderParts.value =selectedOrderParts.value.filter(item => {
                                          for (let key in searchCriteria)
                                          {
                                            if (item[key] !== searchCriteria[key]) {
                                              return true; // Keep this item in the array
                                            }
                                          }
                                          return false; // Remove this item from the array
                                        });
}


</script>


<template>
    <Head title="Items"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>

        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>

                             <Button  class="" severity="info" @click="showFilters=!showFilters"  >

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                  <path v-if="showFilters" stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l7.5-7.5 7.5 7.5m-15 6l7.5-7.5 7.5 7.5" />
                                  <path v-else stroke-linecap="round" stroke-linejoin="round" d="M19.5 5.25l-7.5 7.5-7.5-7.5m15 6l-7.5 7.5-7.5-7.5" />
                                </svg>


                                 </Button>
                            <Toolbar>
                                <template #start>

                                </template>

                                <template #center>
                                 <div class="flex flex-col "
                                   :class="showFilters?'block':'hidden'"
                                 >

                                   <form @submit.prevent="refreshSearch()">
                                      <div class="flex flex-col items-center justify-between space-x-3 overflow-x-auto text-center">

                                        Sales Codes:
                                        <div class="max-w-2xl">
                                        <MultiSelect
                                             v-model="selected_spcodes"
                                             optionLabel="name"
                                             optionValue="code"
                                             :options="props.spcodes"
                                             filter

                                          />
                                        </div>



                                          Records:
                                          <Dropdown
                                             v-model="records"
                                             :options="[10,20,50,100]"
                                             placeholder="10"

                                          />

                                      <div>
                                         Shipment Date:

                                        <input type="date"  class="p-3 hover:border-indigo-500" v-model="shipmentDate" />

                                        <Button type="submit"  label="Go!"/>


                                      </div>
                                    </div>
                                  </form>
                                 <div class="flex flex-row items-center justify-center m-5 text-center">


                                            <input type="text"
                                               v-model="searchKey" placeholder="Search Order" class="m-2 rounded-lg bg-slate-300 text-md" />
                                          <!-- <DownloadButton :link="route('export.confirmations')" /> -->


                                 </div>


                             </div>

                                </template>
                           </Toolbar>
                                    <div class="grid rounded-lg sm:grid-cols-1 md:grid-cols-3">
                                     <div class="col-span-2">


                                       <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="text-white bg-gray-700 ">
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                            Order No.
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Sales Person
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Ship-to Name
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Shipment Date
                                                        </th>


                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part A Items
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part B Items
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part C Items
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part D Items
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr
                                                         v-for="order in orders.data.filter(item => item.confirmations_count !== item.assignments_count)" :key="order.order_no"

                                                         class="font-semibold text-black bg-white hover:bg-gray-300"

                                                     >

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ order.order_no }}
                                                    </td>
                                                    <td class="flex flex-col px-3 py-2 text-xs text-center">
                                                        <span class="text-xs font-bold">{{order.sp_code}}</span>
                                                        <span class="text-xs font-thin">{{order.sp_name}}</span>
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center capitalize bg-yellow-200 rounded-full">
                                                        {{ order.shp_name }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ order.shp_date }}
                                                    </td>


                                                    <td class="flex-col p-1 px-3 py-2 text-xs text-center " v-if="order.part_a!=0">


                                                        <Button  v-show="order.confirm_a" icon="pi pi-eye" severity="warning"
                                                        :badge=order.part_a text raised rounded aria-label="Notification"
                                                        @click="showLines(order.order_no,'A')"

                                                        />

                                                       <span class="p-2 m-1 text-teal-500 rounded-full"
                                                        v-show="checkSelected({'order_no':order.order_no,'part':'A'})">
                                                            Selected
                                                        </span>

                                                    </td>
                                                    <td v-else >

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_b!=0">


                                                        <Button  v-show="order.confirm_b" icon="pi pi-eye" severity="warning" :badge=order.part_b text raised rounded aria-label="Notification" @click="showLines(order.order_no,'B')"/>

                                                          <span class="p-2 m-1 text-teal-500 rounded-full"
                                                        v-show="checkSelected({'order_no':order.order_no,'part':'B'})">
                                                            Selected
                                                        </span>

                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_c!=0">

                                                        <Button  v-show="order.confirm_c" icon="pi pi-eye" severity="warning" :badge=order.part_c text raised rounded aria-label="Notification" @click="showLines(order.order_no,'C')"/>
                                                         <span class="p-2 m-1 text-teal-500 rounded-full"
                                                        v-show="checkSelected({'order_no':order.order_no,'part':'C'})">
                                                            Selected
                                                        </span>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_d!=0">

                                                        <Button  v-show="order.confirm_d" icon="pi pi-eye" :badge=order.part_d severity="warning" text raised rounded aria-label="Notification" @click="showLines(order.order_no,'D')"/>

                                                         <span class="p-1 m-4 text-teal-500 rounded-full"
                                                        v-show="checkSelected({'order_no':order.order_no,'part':'D'})">
                                                            Selected
                                                        </span>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                            </tr>

                            </tbody>
                        </table>
                        </div>


                       <div class="col-span-1 p-1 ml-16 ">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                            <thead class="">

                                <tr class="font-semibold text-black bg-white hover:bg-gray-300"
                                     v-for="part in selectedOrderParts"
                                 >

                                    <td scope="col" class="">
                                       {{part.order_no}}
                                    </td>

                                    <td scope="col" class="px-6 py-3">
                                          {{part.part}}
                                    </td>

                                    <td scope="col" class="px-6 py-3" >
                                          <Button
                                             @click="removePart(part)"
                                             icon="pi pi-times"
                                             severity="danger"
                                             text rounded
                                          />
                                    </td>

                                </tr>

                                <Dropdown
                                  optionLabel="name"
                                  optionValue="id"
                                  :options=assemblers
                                  v-model="assignee"
                                  filter
                                />

                                 <Button
                                   class="w-full text-center space-x"
                                   label="Assign"
                                   severity="success"
                                   :disabled="!assignable"
                                   @click="assign"


                                 />


                                </thead>
                        </table>
                       </div>
                       </div>
                      </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <Pagination :links="orders.meta.links" />
                            </div>
                        </template>
                    </Toolbar>


                </div>




                <!--end of stats bar-->

            </div>

    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" >

     <div class="flex flex-col p-4 rounded-sm">

        <div  class="items-center w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Order Part</div>

            <table>
               <tr v-for="item in filteredLines">
                 <td> {{item.item_description}} </td>
                 <td> {{item.order_qty}} </td>



               </tr>


            </table>
            <Button
                   class="w-full text-center space-x"
                   label="Add"
                   severity="success"
                   @click="pushSelectedPart(filteredLines[0])"
                 />
                <Button
                   class="w-full mt-2 text-center space-x"
                   label="Cancel"
                   severity="warning"
                   @click="showModal=false"
                 />
       </div>

  </Modal>
</AuthenticatedLayout>

</template>
<style lang="scss" scoped>

</style>
