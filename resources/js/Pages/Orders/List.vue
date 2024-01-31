<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import {watch, ref,onMounted} from 'vue';
import Modal from '@/Components/Modal.vue'
import debounce from 'lodash/debounce';
import axios from 'axios';

let ordersArray=ref([]);
let selected_sps=ref([]);

const props= defineProps({
    orders:Object,
    confirmations:Object,
})


let sp_codes=ref([]);

const  extractSpArray=()=> {
  const uniqueValuesMap = new Map();

  for (const item of ordersArray.value) {
    uniqueValuesMap.set(item.sp_code, item.sp_name);
  }

  sp_codes.value = Array.from(uniqueValuesMap.entries());
}






onMounted(() => {
    ordersArray.value=props.orders;
    extractSpArray();


});



let searchKey=ref('')

watch(selected_sps, debounce(() => {
      if (selected_sps.value.length > 0) {
        ordersArray.value = props.orders.filter(order => selected_sps.value.includes(order.sp_code));
      } else {
        ordersArray.value = props.orders;
      }
    }, 300));

watch(searchKey,debounce(()=>{
    if (searchKey.value=='')
     ordersArray.value=props.orders;
    else
     ordersArray.value=props.orders.filter(item=>item.order_no.endsWith(searchKey.value))
},300));

let showModal=ref(false);




const updateConfirmationCount = (order_no, part) => {
  const index = ordersArray.value.findIndex(entry => entry.order_no === order_no);
  if (index !== -1) {
    // Increment the confirmation count of the specified part
    ordersArray.value[index][`${part}_Confirmation_Count`]++; // Assuming your data structure has properties like 'A_Confirmation_Count', 'B_Confirmation_Count', etc.
    //ordersArray.value.sort((a, b) => b[`${part}_Confirmation_Count`] - a[`${part}_Confirmation_Count`]);
  }
};


const ConfirmPrint=async (order_no,part_no)=>{
   await axios.post(route('confirmations.store'),{order_no,part_no})
         .then(response=>{
                           if (response.data.confirmed)
                            {
                               const index = ordersArray.value.findIndex(item => item.order_no === order_no);
                               if (index !== -1) {
                                  ordersArray.value.splice(index, 1);
                                }
                            }

                       });
updateConfirmationCount(order_no,part_no);
}






const records=ref('');
const shipmentDate=ref('');


</script>

<template>
    <Head title="Orders"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800"> Pending confirmation</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>

                            <Toolbar>
                               <template #start>
                                   <div class="p-3 font-semibold text-black bg-teal-400 rounded-md">
                                     Records:{{ orders.length }}
                                   </div>
                               </template>
                                <template #end>

                                <MultiSelect
                                  v-model="selected_sps"
                                  filter
                                  :options="sp_codes"
                                  option-label="1"
                                  option-value="0"
                                  label="Salespersons"
                                />
                                </template>
                                <template #center>


                                 <div class="flex flex-row items-center justify-center m-5 text-center">


                                            <input type="text" v-model="searchKey" placeholder="Search Order" class="m-2 rounded-lg bg-slate-300 text-md" />
                                </div>



                            </template>
                                </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="text-white bg-gray-700 ">
                                                        <!-- <th scope="col" class="px-4 py-2">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-4 py-2">
                                                            Order No.
                                                        </th>
                                                        <th scope="col" class="px-4 py-2 text-center">
                                                            Sales Person
                                                        </th>
                                                        <th scope="col" class="px-4 py-2">
                                                            Ship-to Name
                                                        </th>
                                                        <th scope="col" class="px-4 py-2">
                                                            Shipment Date
                                                        </th>


                                                        <th scope="col" class="px-4 py-2 text-center">
                                                           A
                                                        </th>
                                                        <th scope="col" class="px-4 py-2 text-center">
                                                           B
                                                        </th>
                                                        <th scope="col" class="px-4 py-2 text-center">
                                                           C
                                                        </th>
                                                        <th scope="col" class="px-4 py-2 text-center">
                                                           D
                                                        </th>


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="order in ordersArray" :key="order.order_no"
                                                    class="font-semibold text-black bg-white hover:bg-gray-300">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ order.order_no }}
                                                    </td>
                                                    <td class="flex flex-col px-3 py-2 text-xs text-center">
                                                        <span class="text-xs font-bold">{{order.sp_code}}</span>

                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center capitalize bg-yellow-200 rounded-full">
                                                        {{ order.shp_name }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ order.shp_date }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                                       <Button
                                                        :icon="(order.A_Count<=order.A_Confirmation_Count)?'pi pi-check':'pi pi-question'"
                                                        :severity="(order.A_Count<=order.A_Confirmation_Count)?'success':'warning'"
                                                        v-show="order.A_Count==1"
                                                        :disabled="(order.A_Count<=order.A_Confirmation_Count)"
                                                        @click="ConfirmPrint(order.order_no,'A')"
                                                       />

                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                                         <Button
                                                        :icon="(order.B_Count<=order.B_Confirmation_Count)?'pi pi-check':'pi pi-question'"
                                                        :severity="(order.B_Count<=order.B_Confirmation_Count)?'success':'warning'"
                                                        v-show="order.B_Count==1"
                                                        :disabled="(order.B_Count<=order.B_Confirmation_Count)"
                                                        @click="ConfirmPrint(order.order_no,'B')"
                                                       />
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                                     <Button
                                                        :icon="(order.C_Count<=order.C_Confirmation_Count)?'pi pi-check':'pi pi-question'"
                                                        :severity="(order.C_Count<=order.C_Confirmation_Count)?'success':'warning'"
                                                        v-show="order.C_Count==1"
                                                        :disabled="(order.C_Count<=order.C_Confirmation_Count)"
                                                        @click="ConfirmPrint(order.order_no,'C')"
                                                       />
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                                          <Button
                                                        :icon="(order.D_Count<=order.D_Confirmation_Count)?'pi pi-check':'pi pi-question'"
                                                        :severity="(order.D_Count<=order.D_Confirmation_Count)?'success':'warning'"
                                                        v-show="order.D_Count==1"
                                                        :disabled="(order.D_Count<=order.D_Confirmation_Count)"
                                                        @click="ConfirmPrint(order.order_no,'D')"
                                                       />
                                                    </td>


                                            </tr>

                            </tbody>
                        </table>
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

      <div class="grid p-2 m-4 place-items-center">
         <heading class="w-full p-4 tracking-wide text-black underline bg-slate-200 align-center">Filter Pane</heading>
         <form class="" @submit.prevent="postForm(dynamicObject,dateDynamicObject)">
            <div v-for="item in columnListing" :key="item.name">

                <div v-if="item.type==='string' && item.default_values.length==0" class="p-3">
                    <span class="p-float-label">
                        <InputText :id="item.name"  v-model="dynamicObject[item.name]" class="p-inputtext-sm"/>
                        <label :for="item.name" class="capitalize">{{ item.name }}</label>
                    </span>

                </div>
              <div v-else-if="item.type==='string' &&item.default_values.length>0">
                <Dropdown :options="item.default_values" v-model="dynamicObject[item.name]" />
             </div>

             <div v-if="dateDynamicObject.length!=0">





             </div>
            </div>
            <div v-for="dateData in dateDynamicObject" :key="dateData.id">
                    <label :for="dateData.id">{{ dateData.id }}</label>
                    <input type="date" v-model="dateData.from"/>
                    <input type="date" v-model="dateData.to"/>
                    <!-- <Calendar v-model="dateData.from" selectionMode="range"  :manualInput="true" /> -->
                </div>
        <div class="p-2 text-center">
                    <Button type="Submit" label="Search" />
                </div>
                </form>
      </div>


   <!-- <FilterPane :members="columnListing" :targetRoute="route('order.filter')"/> -->
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
