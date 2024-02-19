<script setup>

import Toolbar from 'primevue/toolbar';
import {watch, ref,onMounted,defineEmits} from 'vue';

import debounce from 'lodash/debounce';

const emit=defineEmits();

function handleButtonClick(orderNo, part,weight) {
    const assignment={ order_no:orderNo, part ,weight };

  emit('add-assignment',assignment );
}

let ordersArray=ref([]);
let assignmentsArray=ref([]);
let selected_sps=ref([]);
let searchKey=ref('');
let sp_codes=ref([]);


const props=defineProps({
    orders:Object,
    assignments:Object
})

const  extractSpArray=()=> {
  const uniqueValuesMap = new Map();
  for (const item of ordersArray.value) {
    uniqueValuesMap.set(item.sp_code, item.sp_name);
  }
  sp_codes.value = Array.from(uniqueValuesMap.entries());
}


onMounted(() => {
    ordersArray.value = props.orders;
    assignmentsArray.value = props.assignments;
    extractSpArray();

});



watch(selected_sps, debounce(() => {
      if (selected_sps.value.length > 0) {
        ordersArray.value = props.orders.filter(order => selected_sps.value.includes(order.sp_code));
      } else {
        ordersArray.value = props.orders;
      }
      extractSpArray();
    }, 300));

watch(searchKey,debounce(()=>{
    // console.log('got')
    if (searchKey.value=='')
     ordersArray.value=props.orders;
    else
     ordersArray.value=props.orders.filter(item=>item.order_no.endsWith(searchKey.value))
    extractSpArray();
},300));

function pushUniqueOrder(orderNo, part,weight) {
    // Check if the object already exists in the array
    const exists = assignmentsArray.value.some(item => item.order_no === orderNo && item.part === part);
    if (!exists) {
        assignmentsArray.value.push({ order_no: orderNo, part: part,weight:weight });
    }

 handleButtonClick(orderNo,part,weight)

}

const checkAssigned=(orderNo,part)=>{
    const exists = assignmentsArray.value.filter(item => item.order_no == orderNo && item.part == part);

    return exists.length>0
}


</script>

<template>

           <div>

            <Toolbar>
                <template #start>
                    <div class="p-3 font-semibold text-black bg-teal-400 rounded-md">
                        Records:{{ orders.length }}
                    </div>
                </template>
                <template #end>
                    <div class="flex flex-row gap-2 ">


                <MultiSelect
                    v-model="selected_sps"
                    filter
                    :options="sp_codes"
                    option-label="1"
                    option-value="0"
                    style="max-width: 200px;"
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

                      <div class="scrollable-container">

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
                                        :icon="checkAssigned(order.order_no,'A')?'pi pi-check':'pi pi-question'"
                                        :severity="checkAssigned(order.order_no,'A')?'success':'warning'"
                                        v-show="order.A_Weight>0"
                                        :label="order.A_Weight"
                                        :disabled="checkAssigned(order.order_no,'A')"
                                        @click="pushUniqueOrder(order.order_no,'A',order.A_Weight)"

                                        />

                                    </td>
                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                        <Button
                                        :icon="checkAssigned(order.order_no,'B')?'pi pi-check':'pi pi-question'"
                                        :severity="checkAssigned(order.order_no,'B')?'success':'warning'"
                                        v-show="order.B_Weight>0"
                                        :label="order.B_Weight"
                                        :disabled="checkAssigned(order.order_no,'B')"
                                        @click="pushUniqueOrder(order.order_no,'B',order.B_Weight)"

                                        />
                                    </td>
                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                       <Button
                                        :icon="checkAssigned(order.order_no,'C')?'pi pi-check':'pi pi-question'"
                                        :severity="checkAssigned(order.order_no,'C')?'success':'warning'"
                                        v-show="order.C_Weight>0"
                                        :label="order.C_Weight"
                                        :disabled="checkAssigned(order.order_no,'C')"
                                        @click="pushUniqueOrder(order.order_no,'C',order.C_Weight)"

                                        />
                                    </td>
                                    <td class="px-3 py-2 text-xs font-bold text-center">
                                           <Button
                                        :icon="checkAssigned(order.order_no,'D')?'pi pi-check':'pi pi-question'"
                                        :severity="checkAssigned(order.order_no,'D')?'success':'warning'"
                                        v-show="order.D_Weight>0"
                                        :label="order.D_Weight"
                                        :disabled="checkAssigned(order.order_no,'D')"
                                        @click="pushUniqueOrder(order.order_no,'D',order.D_Weight)"

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


</template>



<style>
.scrollable-container {
  max-height: 400px; /* Adjust the height as needed */
  overflow-y: auto;
}

.card {
  border: 1px solid #ccc;
  border-radius: 8px;
  margin: 10px;
  padding: 10px;
}

.card-content {
  margin-bottom: 10px;
}
</style>
