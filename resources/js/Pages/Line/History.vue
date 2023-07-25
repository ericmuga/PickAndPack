<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import {debounce,pickby} from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import PickList from 'primevue/picklist';
import Modal from '@/Components/Modal.vue';
import SearchBox from '@/Components/SearchBox.vue';




const sectors= ref([
{'name':'RETAIL','code':'RETAIL'},
{'name':'FAST FOOD','code':'FAST FOOD'},
{'name':'HORECA','code':'HORECA'},
{'name':'STAFF','code':'STAFF'},
{'name':'BLANK','code':'BLANK'},
{'name':'All','code':'All'},
])


const props= defineProps({
    orderLines:Object,

    // printed:Object
})


</script>

<template>
    <Head title="History"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">History</h2>
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
                                    <div class="flex flex-row">
                                        <Pagination :links="orderLines.meta.links" />
                                    </div>



                                </template>

                                <template #end>

                                    <Button type="button" rounded disabled label="Total Lines"  :badge=props.orderLines.meta.total badgeClass="p-badge-danger" outlined class="justify-end" />
                                    <SearchBox model="lines.history"/>

                                </template>
                            </Toolbar>

                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">


                            </div>





                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <div class="flex flex-row w-full ">
                                    <div class="justify-center">

                                    </div>


                                </div>

                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                        <tr class="bg-slate-300">
                                            <!-- <th  scope="col" class="px-6 py-3">Part</th> -->
                                            <th  scope="col" class="px-6 py-3">Order No.</th>
                                            <th  scope="col" class="px-6 py-3">Customer/Ship-to</th>
                                            <th  scope="col" class="px-6 py-3">Salesperson</th>
                                            <th  scope="col" class="px-6 py-3">Item</th>
                                            <th  scope="col" class="px-6 py-3">Description</th>
                                            <th  scope="col" class="px-6 py-3">Ordered qty</th>
                                            <th  scope="col" class="px-6 py-3"> Prepacked Qty</th>
                                            <th  scope="col" class="px-6 py-3">Assembled qty</th>
                                            <th  scope="col" class="px-6 py-3">Customer Spec</th>
                                        </tr>

                                    </thead>


                                    <tbody>
                                        <tr v-for="order in orderLines.data" :key="order.line_no"
                                        class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:text-white hover:bg-slate-400">

                                        <td class="px-3 py-2 text-xs text-center">
                                            {{ order.order_no }}
                                            <!-- <span class="p-1 text-xs text-center">{{ order.order.shp_date }}</span> -->
                                        </td>


                                        <td v-if="order.order.shp_name=='' " class="px-3 py-2 text-xs">
                                            {{ order.order.customer_name }}
                                            <!-- <span class="p-1 m-1 text-xs bg-orange-200 rounded">{{order.order.sector}}</span> -->
                                        </td>
                                        <td v-else class="px-3 py-2 text-xs">
                                            {{ order.order.shp_name }}
                                             <!-- <span class="p-1 m-1 text-xs bg-orange-200 rounded">{{order.order.sector}}</span> -->
                                        </td>
                                        <td class="px-3 py-2 text-xs text-center bg-orange-300 rounded-md">
                                            <p>
                                                {{ order.order.sp_code }}
                                            </p>
                                            <p>
                                                {{ order.order.sp_name }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-2 text-xs">
                                            {{ order.item_no }}
                                        </td>
                                        <td class="px-3 py-2 text-xs">
                                            {{ order.item_description }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-center">
                                            {{ order.order_qty }}
                                        </td>

                                        <!-- <td class="px-3 py-2 text-xs text-center">

                                        </td> -->


                                        <td class="px-3 py-2 text-xs text-center">
                                            {{ order.ass_qty }}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>

                            <Toolbar>
                                <template #center>
                                    <Pagination :links="orderLines.meta.links" />

                                </template>
                            </Toolbar>
                        </div>







                    </div>






                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>

<Modal :show="showModal" @close="showModal=false" :errors="errors">
    <!-- {{ dynamicModalContent  }} -->

    <div class="w-full p-4 font-bold text-center text-white bg-slate-600"> Allocate Prepack</div>
    <div >


        <form @submit.prevent="submitForm()" class="flex flex-col justify-center gap-2 p-5">

            <MultiSelect v-model="form.item"
                        :options="props.items"
                        optionLabel="description"
                        optionValue="item_no"
                        placeholder="Select Items"
                        :maxSelectedLabels="3"
                        class="w-full md:w-20rem"
                        filter
            />

             <MultiSelect v-model="form.sp_code"
                        :options="props.sp_codes"
                    optionLabel="sp_code_and_name"
                        optionValue="sp_code"
                        placeholder="Select Salespersons"
                        :maxSelectedLabels="3"
                        class="w-full md:w-20rem"
                        filter
            />

             <MultiSelect v-model="form.order_no"
                        :options="props.orders"
                       optionLabel="order_customer_ship"
                        optionValue="order_no"
                        placeholder="Select Orders"
                        :maxSelectedLabels="3"
                        class="w-full md:w-20rem"
                        filter
            />
            <!-- <InputText v-model="form.sp_code" placeholder="Salesperson Code"></InputText> -->
            <!-- <InputText v-model="form.order_no" placeholder="Order No."></InputText> -->
            <div class="flex justify-between">
              <label>Posting Date</label>
              <Calendar v-model="form.shp_date" showIcon />
            </div>
            <!-- <input type=date v-model="form.shp_date" placeholder="Shipment Date"/> -->
            <Button  label="Create" severity="primary"  type="submit" :disabled="form.processing" />
            <Button label="Cancel" severity="warning" icon="pi pi-cancel" @click="showModal=false"/>
        </form>


    </div>


</Modal>

</template>
<style>
button:hover {
    cursor: pointer;
}

p:hover {
    cursor: pointer;
}
</style>
