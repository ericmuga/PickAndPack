<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';

import { Inertia } from '@inertiajs/inertia';

import {watch, ref,onMounted} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import debounce from 'lodash/debounce';
import axios from 'axios';

let ordersArray=ref([]);
let confirmationsArray=ref([]);
const props= defineProps({
    orders:Object,
    confirmations:Object,
})

onMounted(() => {
    ordersArray.value=props.orders;
    confirmationsArray.value=props.confirmations;
});

const confirmed = (order_no, part) => {
  let found = confirmationsArray.value.filter(item => {
    return item.order_no === order_no && item.part_no === part;
  });

  return found.length > 0; // Return true if any matches are found, otherwise false
};

let searchKey=ref('')

watch(searchKey,debounce(()=>{
    if (searchKey.value=='')
     ordersArray.value=props.orders;
    else
     ordersArray.value=props.orders.filter(item=>item.order_no.endsWith(searchKey.value))
},300));

let showModal=ref(false);






const CheckPrinted=(Order,Part)=>{
    props.printed
}

const ConfirmPrint=(order_no,part_no)=>{
    axios.post(route('confirmations.store'),{order_no,part_no})
         .then(response=>{
                           if (response.data.confirmed){
                               const index = ordersArray.value.findIndex(item => item.order_no === order_no);
                               if (index !== -1) {
                                  ordersArray.value.splice(index, 1);
                                }

                            }
                            else
                            confirmationsArray.value.push(response.data.confirmation)

         })

}






const records=ref('');
const shipmentDate=ref('');


</script>

<template>
    <Head title="Orders"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800"> Confirmation List</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>

                            <Toolbar>
                                <template #start>


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
                                                        :icon="confirmed(order.order_no,'A')?'pi pi-check':'pi pi-question'"
                                                        :severity="confirmed(order.order_no,'A')?'success':'warning'"
                                                        v-show="order.A==1"
                                                        :disabled="confirmed(order.order_no,'A')"
                                                        @click="ConfirmPrint(order.order_no,'A')"
                                                       />

                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                                         <Button
                                                        :icon="confirmed(order.order_no,'B')?'pi pi-check':'pi pi-question'"
                                                        :severity="confirmed(order.order_no,'B')?'success':'warning'"
                                                        v-show="order.B==1"
                                                        :disabled="confirmed(order.order_no,'B')"
                                                        @click="ConfirmPrint(order.order_no,'B')"
                                                       />
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                                    <Button
                                                        :icon="confirmed(order.order_no,'C')?'pi pi-check':'pi pi-question'"
                                                        :severity="confirmed(order.order_no,'C')?'success':'warning'"
                                                        v-show="order.C==1"
                                                        :disabled="confirmed(order.order_no,'C')"
                                                        @click="ConfirmPrint(order.order_no,'C')"
                                                       />
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                                         <Button
                                                            :icon="confirmed(order.order_no,'D')?'pi pi-check':'pi pi-question'"
                                                            :severity="confirmed(order.order_no,'D')?'success':'warning'"
                                                            v-show="order.D==1"
                                                            :disabled="confirmed(order.order_no,'D')"
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
