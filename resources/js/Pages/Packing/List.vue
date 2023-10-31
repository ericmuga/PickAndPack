<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';
import Pagination from '@/Components/Pagination.vue';
import GeneratePdf from '@/Components/GeneratePdf.vue';
import axios from 'axios';
const search=ref()
watch(search, debounce(()=>{Inertia.post('/order/all',{search:search.value}, {preserveScroll: true})}, 500));

const prop=defineProps({
    orders:Object})
    const inputField=ref(null);

    onMounted(() => {
    inputField.value.focus();
});
let newItem=ref('');

watch( newItem,
debounce( ()=>{Inertia.get(route('packing.index'),{'search':newItem.value})})
,500);


const confirmPack=(order_no,part)=>{ Inertia.get(route('pack.order',{'order_no':order_no,'part_no':part}))}



</script>


<template>
    <Head title="Pack"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Packing</h2>
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
                                        <input type="text" v-model="newItem"  ref="inputField" class="m-2 rounded-lg bg-slate-300 text-md">

                                        <!-- <SearchBox :model="route('order.pack')" /> -->
                                    <div>
                                        <!-- <Pagination :links="orders.meta.links" /> -->
                                    </div>
                                    </div>


                                </template>

                                    <template #end>




                                            <!-- <InputText v-model="search" aria-placeholder="search"/> -->

                                             <div>

                                            </div>
                                            </template>
                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

        <tr class="text-white bg-gray-700 ">
            <!-- <th scope="col" class="px-2 py-2">
                Barcode
            </th> -->
            <th scope="col" class="px-2 py-2">
                Order No.
            </th>
            <th scope="col" class="px-2 py-2 text-center">
                Sales Person
            </th>
            <th scope="col" class="px-2 py-2">
                Ship-to Name
            </th>
            <th scope="col" class="px-2 py-2">
                Shipment Date
            </th>


            <th scope="col" class="px-6 py-3 text-center">
                Part A Items
            </th>
            <th scope="col" class="px-2 py-2 text-center">
                Part B Items
            </th>
            <th scope="col" class="px-2 py-2 text-center">
                Part C Items
            </th>
            <th scope="col" class="px-2 py-2 text-center">
                Part D Items
            </th>


        </tr>
    </thead>
    <tbody>
        <tr v-for="order in orders.data" :key="order.order_no"
        class="font-semibold text-black bg-white hover:bg-gray-300">

        <td class="px-2 py-2 text-xs break-all">
            {{ order.order_no }}
        </td>
        <td class="flex flex-col px-2 py-2 text-xs text-center">
            <span class="text-xs font-bold">{{order.sp_code}}</span>
            <span class="text-xs font-thin">{{order.sp_name}}</span>
        </td>
        <td class="px-2 py-2 text-xs font-bold text-center capitalize bg-yellow-200 rounded-full">
            {{ order.shp_name }}
        </td>
        <td class="px-2 py-2 text-xs font-bold">
            {{ order.shp_date }}
        </td>


        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_a!=0">

            <Button v-show="order.assembled_a"
                    :disabled="order.packed_a"
                    :icon="order.packed_a?'pi pi-check':'pi pi-gift'"
                    :severity="order.packed_a?'success':'danger'"
                    rounded

                    @click="confirmPack(order.order_no,'A')"
                    />
<!--
            <Button  v-show="!order.assembled_a" icon="pi pi-bell" severity="warning" :badge=order.part_a text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'A')"/>
         -->
        </td>
        <td v-else  class="bg-slat-200">

        </td>
        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_b!=0">
            <Button v-show="order.assembled_b"
                    :disabled="order.packed_b"
                    :icon="order.packed_b?'pi pi-check':'pi pi-gift'"
                    :severity="order.packed_b?'success':'danger'"
                    rounded

                    @click="confirmPack(order.order_no,'B')"
                    />
        </td>
        <td v-else  class="bg-slat-200">

        </td>
        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_c!=0">
          <Button v-show="order.assembled_c"
                    :disabled="order.packed_c"
                    :icon="order.packed_c?'pi pi-check':'pi pi-gift'"
                    :severity="order.packed_c?'success':'danger'"
                    rounded

                    @click="confirmPack(order.order_no,'C')"
                    />
        </td>
        <td v-else  class="bg-slat-200">

        </td>
        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_d!=0">
         <Button v-show="order.assembled_d"
                    :disabled="order.packed_d"
                    :icon="order.packed_d?'pi pi-check':'pi pi-gift'"
                    :severity="order.packed_d?'success':'danger'"
                    rounded

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
                        <div class="items-center w-full text-center">

                            <Pagination :links="orders.meta.links" />
                        </div>



                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>
</AuthenticatedLayout>
</template>
