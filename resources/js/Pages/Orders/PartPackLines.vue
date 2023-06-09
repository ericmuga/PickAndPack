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
import {watch, ref,onMounted, nextTick,reactive} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import PickList from 'primevue/picklist';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce'


// import { ref, onMounted } from 'vue';
import { ProductService } from '@/service/ProductService'
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
const error = ref('');
// const upperLimit = ;



watch( newItem,
debounce(
function () {

    //compute upper limit for that item
    const upperLimit=getOrderQty(newItem)

    // console.log(upperLimit)

    if (items.length >= upperLimit) {
        error.value = `Maximum limit (${upperLimit}) reached.`;
        return;
    }

    if (newItem.value.trim() === '') {
        // error.value = 'Input field is empty.';
        return;
    }

    items.push(newItem.value);

    // updateAssembled(newItem);
    count.value = items.length;
    error.value = '';

    // newItem.value = ''; // Clear the input field

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
        'barcode':value.barcode // Use the value with ref/ Extract 'age' key as value with ref
    };
}));

//get the maximum order qty

const getOrderQty=(id)=>{

// console.log(extractedData.value[0])
//

// const foundObject = extractedData.value.find(obj => obj.barcode === id);

for (let i = 0; i < extractedData.value.length; i++)
{

  if (parseInt(extractedData.value[i].barcode)==parseInt(id.value))
  {
    //  console.log('got it')
    return extractedData.value[i].order_qty
  }
  else return 0
}
// Retrieve the order_qty value from the matching object
// return foundObject ? foundObject.order_qty : null;




}

//search the array of objects, and update the assembled quantity on every swipe
const updateAssembled=(id)=>{
     const foundObject=extractedData.value.find(obj=>obj['barcode']===id ||obj['item_no']===id)
    //  console.log(id)
//    if(foundObject ){
//       if (foundObject[ass_qty].value<foundObject[order_qty].value)
//        foundObject[ass_qty].value+=1
//    }
}


// const part =ref();
let showModal=ref(false);



const form=useForm({
    sector:props.previousInput.sector,
    part:props.previousInput.part,
    spcode:props.previousInput.spcode,
    item:props.previousInput.item
})




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
                                    <div>
                                        <Pagination :links="orderLines.meta.links" />
                                    </div>


                                </template>

                                <template #end>

                                    <!-- <Link :href="route('refresh')" class=" mx-auto h-20 w-20 text-center m-5"> -->
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

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                            <div class=" flex flex-row">


                                                <Button type="button" rounded disabled label="Total Lines"  :badge=props.orderLines.meta.total badgeClass="p-badge-danger" outlined  />
                                            </div>

                                            <div class=" grid-cols-2 gap-3">

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
                                                            <tr v-for="order in orderLines.data" :key="order.line_no" class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
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
                                                                <td class="px-3 py-2 text-xs text-center bg-yellow-300 text-black rounded-sm">
                                                                    <p class="rounded  text-white bg-teal-600"> {{ order.ass_qty }}</p>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-span-1 grid place-items-center"  >



                                                    <input type="text" v-model="newItem"  ref="inputField" class="rounded-lg m-2 bg-slate-300 text-md">
                                                    <p v-if="error" class="bg-red-400 text-black font-bold p-3 rounded m-3">{{ error }}</p>
                                                    <ul>
                                                        <li v-for="item in items" :key="item">{{ item }}</li>
                                                    </ul>
                                                    <p>Total count: {{ count }}</p>

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
