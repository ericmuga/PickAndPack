<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';

import { Inertia } from '@inertiajs/inertia';
import Calendar from 'primevue/calendar';

const search=ref('')
const inputField=ref(null);


const props=defineProps({
    orders:Object})



const ordersArray=ref([]);




onMounted(()=>{
    inputField.value.focus();

    const filteredOrders = props.orders.filter(order => order.Complete==0);

ordersArray.value.push(...filteredOrders);
        });





watch(search, debounce(()=>{

if (search.value!='')
    ordersArray.value=props.orders.filter(item=>item.order_no.endsWith(search.value)&&(item.Complete==0));
else  ordersArray.value = props.orders.filter(order =>order.Complete==0);

}, 500));

const ship_date=ref([]);

const confirmPack=(order_no,part)=>{


    Inertia.get(route('assemble.order',{'order_no':order_no,'part_no':part}))


    }

    const colorPicker=(Lines,AssLines)=>{

      if ((AssLines<1)) return 'warning';
      if (Lines>AssLines) return 'info';
      if ((Lines==AssLines)) return 'success'
    }
</script>


<template>
    <Head title="Pack"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800">Assembly</h2>
        </template>

        <div class="py-6">
            <!-- <Modal :show="true" > Hi there </Modal> -->
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>

                                </template>
                                <template #center>
                                    <div>
                                        <!-- <Pagination :links="orderLines.meta.links" /> -->
                                        <input  type="text" v-model="search"
                                                ref="inputField"
                                                placeholder="Search Order"
                                                class="m-2 text-center rounded-lg bg-slate-300 text-md"
                                        >


                                        <!-- <SearchBox :model="route('order.pack')" /> -->
                                    <div>
                                        <!-- <Pagination :links="orders.meta.links" /> -->
                                    </div>
                                    </div>


                                </template>

                                    <template #end>




                                            <!-- <InputText v-model="search" aria-placeholder="search"/> -->


                                            </template>
                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="text-white bg-gray-700">
                                                        <th scope="col" class="px-2 py-2">
                                                            Order No.
                                                        </th>
                                                        <th scope="col" class="px-2 py-2 text-center">
                                                            Sales Person
                                                        </th>
                                                        <th scope="col" class="px-2 py-2 ">
                                                            Ship-to Name
                                                        </th>
                                                        <th scope="col" class="px-2 py-2">
                                                            Shipment Date
                                                        </th>

                                                        <th scope="col" class="px-2 py-2 text-center">
                                                            A
                                                        </th>
                                                        <th scope="col" class="px-2 py-2 text-center">
                                                            B
                                                        </th>
                                                        <th scope="col" class="px-2 py-2 text-center">
                                                            C
                                                        </th>
                                                        <th scope="col" class="px-2 py-2 text-center">
                                                            D
                                                        </th>


                                                    </tr>
                                                </thead>
                                                <tbody v-if="ordersArray.length>0">

                                                    <tr v-for="order in ordersArray" :key="order.order_no"
                                                    class="font-semibold text-black bg-white hover:bg-gray-300">

                                                    <td class="px-2 py-2 text-xs break-all">
                                                        {{ order.order_no }}
                                                    </td>
                                                    <td class="flex flex-col px-2 py-2 text-xs text-center ">
                                                        <span class="text-xs font-bold">{{order.sp_code}}</span>
                                                        <span class="text-xs font-semibold text-red-500 bg-gray-100 rounded-lg">{{order.sp_name}}</span>
                                                    </td>
                                                    <td class="px-2 py-2 text-xs font-bold text-center text-black capitalize bg-yellow-200 rounded-full">
                                                        {{ order.shp_name }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ order.shp_date }}
                                                    </td>


                                                    <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.A_Assignment_Count!=0">

                                                        <Button
                                                        v-show="order.A_Assignment_Count>0"
                                                        :icon="order.A_Assignment_Count>=A_Assembly_Count?'pi pi-check':'pi pi-cart-plus'"
                                                        :severity=colorPicker(order.A_Lines,order.A_AssLine)

                                                        rounded
                                                        :label="pack"
                                                        @click="confirmPack(order.order_no,'A')"
                                                        />

                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.B_Assignment_Count!=0">

                                                        <Button
                                                        v-show="order.B_Assignment_Count>0"
                                                        :icon="order.B_Assignment_Count>=B_Assembly_Count?'pi pi-check':'pi pi-cart-plus'"
                                                        :severity=colorPicker(order.B_Lines,order.B_AssLine)

                                                        rounded
                                                        :label="pack"
                                                        @click="confirmPack(order.order_no,'B')"
                                                        />

                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.C_Assignment_Count!=0">

                                                        <Button
                                                        v-show="order.C_Assignment_Count>0"
                                                        :icon="order.C_Assignment_Count>=C_Assembly_Count?'pi pi-check':'pi pi-cart-plus'"
                                                        :severity=colorPicker(order.C_Lines,order.C_AssLine)

                                                        rounded
                                                        :label="pack"
                                                        @click="confirmPack(order.order_no,'C')"
                                                        />

                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.D_Assignment_Count!=0">

                                                        <Button
                                                        v-show="order.D_Assignment_Count>0"
                                                        :icon="order.D_Assignment_Count>=D_Assembly_Count?'pi pi-check':'pi pi-cart-plus'"
                                                        :severity=colorPicker(order.D_Lines,order.D_AssLine)

                                                        rounded
                                                        :label="pack"
                                                        @click="confirmPack(order.order_no,'D')"
                                                        />

                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                            </tr>

                                            </tbody>
                                            </table>



                                        </div>






                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>
</AuthenticatedLayout>
</template>
