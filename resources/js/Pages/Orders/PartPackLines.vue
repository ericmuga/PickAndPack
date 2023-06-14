<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
// import {debounce} from 'lodash/debounce';
import {watch, ref,onMounted, nextTick,reactive,computed} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import PickList from 'primevue/picklist';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce'
// import computed from 'vue'


// import { ref, onMounted } from 'vue';
// import { ProductService } from '@/service/ProductService'
import { useOrderStore } from '@/service/OrderStore'
// import { useConfirm } from "primevue/useconfirm";

// const confirm = useConfirm();

const products = ref(null);
// const orders=ref(null);
const inputField=ref(null);

const props= defineProps({
    orderLines:Object,
    selectedPart:Object,
    previousInput:Object,
    // printed:Object
})

onMounted(() => {
    inputField.value.focus();
});

const newItem = ref('');
const items = reactive([]);
const count = ref(0);
const scanError = ref('');
let currentItem=ref('');
let currentCount=ref('');
let currentOrderQty=ref('');

// const upperLimit = ;

const isActive = ref(false);

const getSelected=(id)=>(id=currentItem)?'bg-slate-400 text-white':'bg-white border-b dark:bg-gray-900 dark:border-gray-700'



watch( newItem,
debounce(
function () {

    //compute upper limit for that item
    const upperLimit=getOrderDetail(newItem,'order_qty')
    const assembledQty=getOrderDetail(newItem,'ass_qty')

    if (upperLimit.value==0) { scanError.value='Item not found!'; return;}
    // console.log(upperLimit)

    if (assembledQty > upperLimit) {
        scanError.value = `Maximum limit (${upperLimit}) reached.`;
        return;
    }

    if (newItem.value.trim() === '') {
        // error.value = 'Input field is empty.';
        return;
    }

    // items.push(newItem.value);

    updateAssembled(newItem);
    count.value = assembledQty;
    scanError.value = '';

    newItem.value = ''; // Clear the input field

    // Use $nextTick to clear the input field after the DOM update
    // This ensures that the input field is cleared immediately
    // after the item is appended
    // Note: Import 'nextTick' from 'vue' if using a separate script block

}
,300));




let scannedItems=ref([])


nextTick(() => {
    newItem.value = '';
});


const extractedData = ref(Object.entries(props.orderLines.data).map(([key, value]) => {

    return {
        'order_no':value.order_no,
        'item_no': value.item_no, // Use the key as the label
        'order_qty': value.order_qty ,// Use the value with ref/ Extract 'age' key as value with ref
        'ass_qty': value.ass_qty,
        'barcode':value.barcode,
        'part':value.part,
        'item_description':value.item_description,
        'customer_spec':value.customer_spec,
        'line_no':value.line_no

         // Use the value with ref/ Extract 'age' key as value with ref
    };
}));

//get the maximum order qty

const getOrderDetail=(id,detail)=>{


    if (id.value!='')
    {
            for (let i = 0; i < extractedData.value.length; i++)
            {

                if (parseInt(extractedData.value[i].barcode)==parseInt(id.value))
                {

                    //console.log(extractedData.value[i].order_qty);

                    switch (detail.value) {
                        case 'order_qty':return extractedData.value[i].order_qty;
                            break;
                    case 'ass_qty':return extractedData.value[i].ass_qty;
                            break;

                        default:
                            break;
                    }


                }
               else return 0
            }
            // Retrieve the order_qty value from the matching object
    }


}

//search the array of objects, and update the assembled quantity on every swipe
const updateAssembled=(id)=>{
    if (id.value!='')
    {
            for (let i = 0; i < extractedData.value.length; i++)
            {

            if (parseInt(extractedData.value[i].barcode)==parseInt(id.value))
            {

               if (extractedData.value[i].order_qty>extractedData.value[i].ass_qty)
                {
                    extractedData.value[i].ass_qty++;

                }
                currentItem= extractedData.value[i].item_description;
                currentOrderQty=extractedData.value[i].order_qty
                currentCount=extractedData.value[i].ass_qty

            }

            // Retrieve the order_qty value from the matching object
    }

}
}


// const part =ref();
let showModal=ref(false);



const form=useForm({
    sector:props.previousInput.sector,
    part:props.previousInput.part,
    spcode:props.previousInput.spcode,
    item:props.previousInput.item
});




// const submitForm=()=>{form.get(route('orders.lines'))}

// const prepack=()=>{ form.post(route('orders.prepack'))}


</script>

<template>
    <Head title="Orders"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Parts</h2>
        </template>

        <div class="py-3">
            <!-- <Modal :show="true" > Hi there </Modal> -->
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>

                                </template>
                                <template #center>
                                    <div flex flex-row>
                                        <Pagination :links="orderLines.meta.links" />

                                                <Button type="button" rounded disabled label="Total Lines"  :badge=props.orderLines.meta.total badgeClass="p-badge-danger" outlined  />
                                    </div>



                                </template>

                                <template #end>

                                    <!-- <Link :href="route('refresh')" class="w-20 h-20 m-5 mx-auto text-center "> -->
                                        <!-- <img src="/img/refresh.png" /> -->
                                        <!-- <Button icon="pi pi-heart" severity="help" rounded aria-label="Favorite" /> -->
                                        <!-- <Button icon="pi pi-refresh" severity="primary" rounded /> -->
                                        <!-- <img src="/img/scanner.jpg" /> -->
                                        <!-- </Link> -->



                                        <!-- <InputText v-model="search" aria-placeholder="search"/> -->

                                    </template>
                                </Toolbar>

                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">


                                </div>


                                <TabView>
                                    <TabPanel header="Order Lines">
                                        <div class="flex flex-row items-center justify-center w-full gap-1 text-center">

                                                    <input type="text" v-model="newItem"  ref="inputField" class="m-2 rounded-lg bg-slate-300 text-md">
                                                                <p v-if="scanError" class="p-3 m-3 font-bold text-black bg-red-400 rounded">{{ scanError }}</p>
                                        </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <div class="w-full m-2 text-center " v-if="currentItem!=''">
                                                <Button type="button" rounded disabled :label="currentItem"   outlined  />
                                                <Button type="button" rounded disabled label="Ordered"  :badge=currentOrderQty badgeClass="p-badge-info" outlined  />
                                                <Button type="button" rounded disabled label="Assembled"  :badge=currentCount badgeClass="p-badge-success" outlined  />
                                            </div>


                                            <div class="grid-cols-2 gap-3 ">

                                                <div class="col-span-1">
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                            <tr class="bg-slate-300">
                                                                <th  scope="col" class="px-6 py-3">Part</th>
                                                                <th  scope="col" class="px-6 py-3">Order No.</th>
                                                                <th  scope="col" class="px-6 py-3">Barcode</th>
                                                                <th  scope="col" class="px-6 py-3">Description</th>
                                                                <th  scope="col" class="px-6 py-3">Customer Spec</th>
                                                                <th  scope="col" class="px-6 py-3">Ordered qty</th>
                                                                <th  scope="col" class="px-6 py-3">Assembled qty</th>
                                                            </tr>

                                                        </thead>


                                                        <tbody>
                                                            <tr v-for="order in extractedData" :key="order.line_no"
                                                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-400 hover:text-white ">
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ order.part }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ order.order_no }}
                                                                </td>

                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ order.barcode}}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ order.item_description }}
                                                                </td>

                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ order.customer_spec }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs text-center">
                                                                    {{ order.order_qty }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs text-center text-black bg-yellow-300 rounded-sm">
                                                                    <input class="text-center text-white bg-teal-600 rounded" v-model="order.ass_qty"/>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="grid col-span-1 place-items-center"  >




                                                    <!-- <ul>
                                                        <li v-for="item in items" :key="item">{{ item }}</li>
                                                    </ul>
                                                    <p>Total count: {{ count }}</p> -->

                                                </div>
                                            </div>
                                            <Toolbar>
                                                <template #center>
                                                    <Pagination :links="orderLines.meta.links" />

                                                </template>
                                            </Toolbar>
                                        </div>
                                    </TabPanel>



                                </TabView>




                            </div>




                            <!--end of stats bar-->

                        </div>
                    </div>
                </div>
            </div>
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
