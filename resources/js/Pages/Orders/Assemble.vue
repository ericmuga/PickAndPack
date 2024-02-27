<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';
import Swal from 'sweetalert2'
import SearchBox from '@/Components/SearchBox.vue'
import { Inertia } from '@inertiajs/inertia';
// import Intertia from

const search=ref('')
const inputField=ref(null);


const props=defineProps({
    orders:Object})


    onMounted(() => {
     inputField.value.focus();
     ordersArray.value=ref(props.orders);
});
const ordersArray=ref(props.orders);
watch(search, debounce(()=>{

  if (ordersArray.value.length>0)
    if (search.value!='')
  {
        ordersArray.value=ordersArray.value.filter(item=>item.order_no.endsWith(search.value))
    } else {
        ordersArray.value = props.orders;
    }
}, 500));


onMounted(()=>{
    //ordersArray with only unfulfilled orders
    for (let j = 0; j < props.orders.length; j++) {
            if (  (
                    parseInt(props.orders[j].A_Assignment_Count)+
                    parseInt(props.orders[j].B_Assignment_Count)+
                    parseInt(props.orders[j].C_Assignment_Count)+
                    parseInt(props.orders[j].D_Assignment_Count)

                )
                    <
                (
                    parseInt(props.orders[j].A_Assembly_Count)+
                    parseInt(props.orders[j].B_Assembly_Count)+
                    parseInt(props.orders[j].C_Assembly_Count)+
                    parseInt(props.orders[j].D_Assembly_Count)

                )
                )

                    {

                        ordersArray.value.push(props.orders[j])
                    }

         };
        });

// let newItem=ref('');
// watch( newItem,
// debounce( ()=>{Inertia.get(route('assembly.index'),{'search':newItem.value})})
// ,500);


const confirmPack=(order_no,part)=>{


    Inertia.get(route('assemble.order',{'order_no':order_no,'part_no':part}))


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
    <tbody>
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
               :severity="order.A_Assembly_Count>0?'success':'warning'"
               :disabled="order.A_Assembly_Count>0"
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
               :severity="order.B_Assembly_Count>0?'success':'warning'"
               :disabled="order.B_Assembly_Count>0"
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
               :severity="order.C_Assembly_Count>0?'success':'warning'"
               :disabled="order.C_Assembly_Count>0"
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
               :severity="order.D_Assembly_Count>0?'success':'warning'"
               :disabled="order.D_Assembly_Count>0"
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
