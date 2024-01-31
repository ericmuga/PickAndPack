<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import {watch, ref,onMounted} from 'vue';
import Modal from '@/Components/Modal.vue'
import debounce from 'lodash/debounce';
import axios from 'axios';

let ordersArray=ref([]);
let selected_sps=ref([]);
let searchKey=ref('');
let sp_codes=ref([]);

const props= defineProps({
    orders:Object,
    confirmations:Object,
})
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
    ordersArray.value[index][`${part}_Confirmation_Count`]++;
  }
};

const ConfirmPrint= (order_no,part_no)=>{
    axios.post(route('confirmations.store'),{order_no,part_no})
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

const form=ref({
    start_date:null,
    end_date:null,

});

 const formData = new FormData();


//  const postForm=()=>{
//        formData.append('start_date',form.value.start_date );
//        formData.append('end_date',form.value.end_date);
//        axios.get(route('registry.download',formData))

//             .catch((response)=> console.log(response))
//  }
const postForm = () => {
    const queryParams = new URLSearchParams({
        start_date: form.value.start_date,
        end_date: form.value.end_date
    });

    axios.get(route('registry.download') + '?' + queryParams)
        .then(response => {

        })
        .catch(error => {
            console.error(error);
        });
    showModal.value=false
}


</script>

<template>
    <Head title="Registry"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800 rounded"> Pending confirmation</h2>
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
                                 <div class="flex flex-row gap-2 ">
                                   <Button
                                      label="Download"
                                      severity="success"
                                      class="mx-2"
                                      @click="showModal=true"
                                    />

                                    <MultiSelect
                                    v-model="selected_sps"
                                    filter
                                    :options="sp_codes"
                                    option-label="1"
                                    option-value="0"
                                    label="Salespersons"
                                    />
                                 </div>

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
