<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
// import Button from 'primevue/button';

import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';

import Swal from 'sweetalert2'

import SearchBox from '@/Components/SearchBox.vue'

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
debounce( ()=>{Inertia.get(route('assembly.index'),{'search':newItem.value})})
,500);


const confirmPack=(order_no,part)=>{ Inertia.get(route('assemble.order',{'order_no':order_no,'part_no':part}))}
</script>


<template>
    <Head title="Pack"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Assembly</h2>
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
                                        <input type="text" v-model="newItem"  ref="inputField" placeholder="Search Order" class=" text-center m-2 rounded-lg bg-slate-300 text-md">

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

        <tr class="bg-gray-700 text-white">
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
        class="bg-white text-black hover:bg-gray-300 font-semibold">

        <td class="px-2 py-2 text-xs break-all">
            {{ order.order_no }}
        </td>
        <td class="flex flex-col px-2 py-2 text-xs text-center  ">
            <span class="text-xs font-bold">{{order.sp_code}}</span>
            <span class="text-xs bg-gray-100 rounded-lg font-semibold text-red-500">{{order.sp_name}}</span>
        </td>
        <td class="px-2 py-2 text-xs font-bold text-center text-black capitalize bg-yellow-200 rounded-full">
            {{ order.shp_name }}
        </td>
        <td class="px-3 py-2 text-xs font-bold">
            {{ order.shp_date }}
        </td>

        
        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_a!=0">

            <Button v-show="order.assigned_a" icon="pi pi-cart-plus" severity="warning" rounded :label="pack" @click="confirmPack(order.order_no,'A')" />
<!--
            <Button  v-show="!order.confirm_a" icon="pi pi-bell" severity="warning" :badge=order.part_a text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'A')"/>
         -->
        </td>
        <td v-else  class="bg-slat-200">

        </td>
        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_b!=0">
            <Button v-show="order.assigned_b" icon="pi pi-cart-plus" severity="warning" rounded :label="pack" @click="confirmPack(order.order_no,'B')" />

            <!-- <Button  v-show="!order.confirm_b" icon="pi pi-bell" severity="warning" :badge=order.part_b text raised rounded aria-label="Notification" @click="
            ConfirmPrint(order.order_no,'B')"/> -->
        </td>
        <td v-else  class="bg-slat-200">

        </td>
        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_c!=0">
            <Button v-show="order.assigned_c" icon="pi pi-cart-plus" severity="warning" rounded :label="pack" @click="confirmPack(order.order_no,'C')" />
            <!-- <Button  v-show="!order.confirm_c" icon="pi pi-bell" severity="warning" :badge=order.part_c text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'C')"/> -->
        </td>
        <td v-else  class="bg-slat-200">

        </td>
        <td class="p-1 px-2 py-2 text-xs text-center " v-if="order.part_d!=0">
            <Button v-show="order.assigned_d" icon="pi pi-cart-plus" severity="warning" rounded :label="pack" @click="confirmPack(order.order_no,'D')" />
            <!-- <Button  v-show="!order.confirm_d" icon="pi pi-bell" :badge=order.part_d severity="warning" text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'D')"/> -->
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
