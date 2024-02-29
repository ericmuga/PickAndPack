

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { onMounted,ref,reactive,watch,computed,onUnmounted } from 'vue';
import SearchBox from '@/Components/SearchBox.vue'
import debounce from 'lodash/debounce'
import ProgressBar from 'primevue/progressbar';

import { useSearchArray } from '@/Composables/useSearchArray';
import Swal from 'sweetalert2'
import { Inertia } from '@inertiajs/inertia';

const inputField=ref(null);
const newItem = ref('');
const items = reactive([]);
const count = ref(0);
let scanError = ref('');

const calculateVesselCount = (dataArray) => {
  return dataArray.reduce((sum, item) => sum + parseInt(item.vessel_count), 0);
};


const props= defineProps({
    packingLines:Object,
    session:Object,
    orders:Object,
    // pick_no:String,

})

function getCountForCode(array, codeToFind) {
  let count = 0;
  array.forEach(entry => {
    if (entry.code === codeToFind) {
      count++;
    }
  });
  return count;
}

function getSumOfCounts(array) {
  const codeCounts = {};
  array.forEach(entry => {
    codeCounts[entry.code] = (codeCounts[entry.code] || 0) + 1;
  });

  let sum = 0;
  for (const code in codeCounts) {
    sum += codeCounts[code];
  }

  return sum;
}




</script>

<template>
 <Head title="Loading Sheet"/>

    <AuthenticatedLayout>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800">Loading</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>


                                <template #center>
                                    <div class="flex flex-row space-x-3">
                                        <span class="p-3 text-white rounded-lg bg-lime-500">{{ session.data.vehicle }}</span>
                                        <h2 class="flex flex-row text-xl font-bold tracking-wide text-red-500"> {{ session.data.sp[0].Name }}
                                        </h2>
                                        <span class="p-3 text-white bg-indigo-700 rounded-lg">{{session.data.loader }}</span>
                                    </div>


                                </template>


                        </Toolbar>


                        <div class="text-center ">

                         <div class="m-4 text-center shadow-md">
                            <input type="text" v-model="newItem"  ref="inputField" placeholder="Scan Vessel" class="m-2 rounded-lg bg-slate-300 text-md">
                             <p v-if="scanError" class="p-3 m-3 font-bold text-black bg-red-400 rounded">{{ scanError }}</p>


                                                  <div class="relative overflow-x-auto overflow-y-auto text-center shadow-md sm:rounded-lg" style="height: 400px;">


                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="text-white bg-gray-700 ">
                                                        <!-- <th scope="col" class="px-4 py-2">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-4 ">
                                                            Order No.
                                                        </th>

                                                        <th scope="col" class="px-4 ">
                                                            Ship-to Name
                                                        </th>

                                                        <th scope="col" class="px-4 text-center">
                                                            A Vessels
                                                        </th>
                                                        <th scope="col" class="px-4 text-center">
                                                            B Vessels
                                                        </th>
                                                        <th scope="col" class="px-4 text-center">
                                                            C Vessels
                                                        </th>
                                                        <th scope="col" class="px-4 text-center">
                                                            D Vessels
                                                        </th>
                                                        <th scope="col" class="px-4 text-center">
                                                           Expected Vessels
                                                        </th>

                                                        <th scope="col" class="px-4 text-center">
                                                            Loaded
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="order in orders.data" :key="order.order_no"
                                                    class="font-semibold text-black bg-white hover:bg-gray-300">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ order.order_no }}
                                                    </td>
                                                        <td class="px-3 py-2 text-xs font-bold text-center capitalize">
                                                        {{ order.shp_name }}
                                                    </td>

                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_a!=0">

                                                        <ul v-if="order.vessel_a.length>0">
                                                            <li v-for="ves in order.vessel_a" :key="ves.code">
                                                            {{ ves.code+'-'+getCountForCode(order.vessel_a,ves.code)}}
                                                        </li>

                                                        </ul>

                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_b!=0">
                                                        <!-- <Button v-show="order.confirm_b" icon="pi pi-check" severity="info" rounded :label="order.part_b" disabled /> -->

                                                        <!-- <Button  v-show="!order.confirm_b" icon="pi pi-bell" severity="warning" :badge=order.part_b text raised rounded aria-label="Notification" @click="
                                                        ConfirmPrint(order.order_no,'B')"/> -->
                                                        <ul v-if="order.vessel_b.length>0">
                                                            <li v-for="ves in order.vessel_b" :key="ves.code">
                                                            {{ ves.code+'-'+getCountForCode(order.vessel_b,ves.code)}}
                                                        </li>
                                                        </ul>

                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_c!=0">
                                                        <!-- <Button v-show="order.confirm_c" icon="pi pi-check" severity="info" rounded :label="order.part_c" disabled /> -->

                                                        <!-- <Button  v-show="!order.confirm_c" icon="pi pi-bell" severity="warning" :badge=order.part_c text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'C')"/> -->

                                                        <ul v-if="order.vessel_c.length>0">
                                                            <li v-for="ves in order.vessel_c" :key="ves.code">
                                                            {{ ves.code+'-'+getCountForCode(order.vessel_c,ves.code)}}
                                                        </li>

                                                    </ul>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_d!=0">
                                                        <!-- <Button v-show="order.confirm_d" icon="pi pi-check" severity="info" rounded :label="order.part_d" disabled /> -->

                                                        <!-- <Button  v-show="!order.confirm_d" icon="pi pi-bell" :badge=order.part_d severity="warning" text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'D')"/> -->
                                                       <ul v-if="order.vessel_d.length>0">
                                                            <li v-for="ves in order.vessel_d" :key="ves.code">
                                                            {{ ves.code+'-'+getCountForCode(order.vessel_d,ves.code)}}
                                                        </li></ul>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                   <td class="m-2 text-center bg-yellow-200 rounded-full">
                                                    {{getSumOfCounts(order.vessel_a)+
                                                      getSumOfCounts(order.vessel_b)+
                                                      getSumOfCounts(order.vessel_c)+
                                                      getSumOfCounts(order.vessel_d)
                                                     }}
                                                   </td>
                                                   <td class="p-2 text-center bg-teal-300 rounded-md">
                                                     0
                                                     <ProgressBar value="0/100" />
                                                   </td>


                                            </tr>

                            </tbody>
                        </table>
                    </div>
                         </div>

                        </div>
           </div>
</div>


         </div>
        </div>


    </div>

    </AuthenticatedLayout>
</template>



<style lang="scss" scoped>

</style>
