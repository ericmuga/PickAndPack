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
import Swal from 'sweetalert2'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce'
import ProgressBar from 'primevue/progressbar';
import { useSearchArray } from '@/Composables/useSearchArray';

const inputField=ref(null);
const scanItem=ref(null);

const props= defineProps({
    orderLines:Object,
    pick_no:String,

})

onMounted(() => {
    inputField.value.focus();
});

const newItem = ref('');
const items = reactive([]);
const count = ref(0);
let scanError = ref('');
let currentItem=ref('');
let currentCount=ref('');
let currentOrderQty=ref('');
let showModal=ref(false);
let closeModal=ref(true);
const isActive = ref(false);

const extractedData = ref(Object.entries(props.orderLines).map(([key, value]) => {

    return {

        'total_order_qty': value.total_order_qty ,// Use the value with ref/ Extract 'age' key as value with ref
        'prepacked_qty': value.prepacked_qty ,// Use the value with ref/ Extract 'age' key as value with ref
        'barcode':value.barcode,
        'item_no':value.item_no,
        'item_description':value.item_description,

    };
}));

const searchKey = ref('');
const searchValue = ref('');
const {searchByBarcodeOrItemNo } = useSearchArray(extractedData)
const searchResult = ref(0);

const assembledArray=ref([]);



watch( newItem,
 debounce(
            function () {
                if (newItem.value.trim()!='' ){
                            scanError.value = '';

                       searchResult.value= searchByBarcodeOrItemNo((newItem.value.toUpperCase()).trim())
                       if (searchResult.value!=0)
                        {

                            if (parseFloat(searchResult.value.total_order_qty)>parseFloat(searchResult.value.prepacked_qty))
                            {
                                showModal.value=true
                                updateScannedItem(searchResult.value)



                            }
                            else scanError.value=`Maximum limit ${searchResult.value.total_order_qty} reached.`;

                        }
                        else scanError.value=`Item Not found!`;
                    }
                        }
            ,300)

        );


const form=useForm({
   item_no:'',
   total_order_qty:0,
   prepacked_qty:0,
   assembled_qty:0,
   item_description:'',
   batch_no:''


});

const ItemInAssembledArray=(item_no)=>{
   const existingItemIndex= assembledArray.value.findIndex(item => item.item_no === item_no)
   return(existingItemIndex!==-1)

}

const submitForm=()=>{
   //push item into assembled array

    const existingItemIndex = assembledArray.value.findIndex(item => item.item_no === form.item_no);

    if (existingItemIndex !== -1) {
      // If the key already exists, update the value
    //   alert('here')
      assembledArray.value[existingItemIndex].item_no = form.item_no;
      assembledArray.value[existingItemIndex].assembled_qty = form.assembled_qty;
      assembledArray.value[existingItemIndex].batch_no = form.batch_no;
    } else {
      // If the key doesn't exist, push a new key-value pair
      assembledArray.value.push({ 'item_no':form.item_no,
                                   'assembled_qty':form.assembled_qty,
                                   'total_order_qty':form.total_order_qty,
                                   'prepacked_qty':form.prepacked_qty,
                                   'item_description':form.item_description,
                                   'barcode':form.barcode,
                                    'item_no':form.item_no,
                                    'batch_no':form.batch_no
                                });
    }

    showModal.value=false
    newItem.value = '';
    inputField.value.focus();
}



const updateScannedItem =(item)=>{
//update form
    form.item_no=item.item_no
    form.barcode=item.barcode
    form.total_order_qty=item.total_order_qty
    form.prepacked_qty=item.prepacked_qty
    form.assembled_qty=item.total_order_qty-item.prepacked_qty
    form.pick_no=props.pick_no
    form.item_description=item.item_description
    form.batch_no=''



}



const closeAssembly=()=>{

     Swal.fire({
                                        title: 'Are you sure?',
                                        text: "Assembled orders may not be undone!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Close Assembly!'
                                        }).then((result) => {
                                            if (result.isConfirmed) {Inertia.post(route('picks.store'),{'data':assembledArray.value,'pick_no':props.pick_no});}
                        })





}



</script>

<template >
    <Head title="Orders"/>

    <AuthenticatedLayout  @add="showModal=true">
        <!-- <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Parts</h2>
        </template> -->

        <div class="py-3">
            <!-- <Modal :show="true" > Hi there </Modal> -->
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">

                        <!--stats bar -->

                        <!-- <Button
                          label="test"
                         @click="logOrderLines()"
                        ></Button> -->

                        <div>
                            <Toolbar>
                                <template #start>

                                </template>
                                <template #center>
                                    <div flex flex-row>
                                        <!-- <Pagination :links="orderLines.meta.links" /> -->
                                                <!-- <Button type="button" rounded disabled label="Total Lines"  :badge=props.orderLines.meta.total badgeClass="p-badge-danger" outlined  /> -->
                                                <Button
                                                    class="justify-end"
                                                   label="Close Assembly"
                                                   @click="closeAssembly()"


                                                />
                                    </div>



                                </template>

                                <template #end>



                                    </template>
                                </Toolbar>

                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">


                                </div>


                                <TabView>
                                    <TabPanel header="Order Lines">
                                        <div class="flex flex-row items-center justify-center w-full gap-1 text-center">

                                                    <input type="text" v-model="newItem"  ref="inputField" placeholder="Scan Item" class="m-2 rounded-lg bg-slate-300 text-md">
                                                                <p v-if="scanError" class="p-3 m-3 font-bold text-black bg-red-400 rounded">{{ scanError }}</p>
                                        </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <div class="w-full m-2 text-center">


                                               {{assembledArray.length }} / {{ orderLines.length }}

                                               <ProgressBar :value="Math.round((assembledArray.length)/(orderLines.length)*100)" />

                                            </div>



                                            <div class="grid grid-cols-2 gap-3 ">

                                                <div class="col-span-1">
                                                    <div  class="w-full p-3 m-2 text-center text-white bg-orange-200"> Ordered</div>
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                                                        <!-- <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                            <tr class="bg-slate-300">
                                                                <th  scope="col" class="px-6 py-3">Item No.</th>
                                                                <th  scope="col" class="px-6 py-3">Item</th>

                                                                <th  scope="col" class="px-6 py-3">Barcode</th>
                                                                <th  scope="col" class="px-6 py-3">Ordered qty</th>
                                                                <th  scope="col" class="px-6 py-3">Prepack qty</th>
                                                                <th  scope="col" class="px-6 py-3">Assembled qty</th>
                                                            </tr>

                                                        </thead> -->


                                                        <tbody>
                                                            <tr

                                                              v-for="line in orderLines" :key="line.item_description"

                                                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-400 hover:text-white ">
                                                                <div v-if="!ItemInAssembledArray(line.item_no)" class="flex justify-between">
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.item_no }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.item_description }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.barcode }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.total_order_qty }}
                                                                </td>

                                                                <!-- <td class="px-3 py-2 text-xs">
                                                                    {{ line.prepacked_qty}}
                                                                </td> -->

                                                                <!-- <td class="px-3 py-2 text-xs text-center text-black bg-yellow-300 rounded-sm">
                                                                    <input
                                                                      class="text-center rounded"
                                                                      v-model="order.ass_qty"
                                                                      :disabled="order.prepacked_qty==order.total_order_qty"
                                                                    />
                                                                </td> -->
                                                                </div>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-span-1"  >
                                             <div  class="w-full p-3 m-2 text-center text-white bg-slate-400"> Assembled</div>
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                                                        <!-- <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                            <tr class="bg-slate-300">
                                                                <th  scopes="col" class="px-6 py-3">Item No.</th>
                                                                <th  scope="col" class="px-6 py-3">Item</th>

                                                                <th  scope="col" class="px-6 py-3">Barcode</th>
                                                                <th  scope="col" class="px-6 py-3">Ordered qty</th>
                                                                <th  scope="col" class="px-6 py-3">Prepack qty</th>
                                                                <th  scope="col" class="px-6 py-3">Assembled qty</th>
                                                            </tr>

                                                        </thead> -->


                                                        <tbody>

                                                            <tr v-for="line in assembledArray" :key="line.item_description"
                                                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-400 hover:text-white ">

                                                                <div v-if="ItemInAssembledArray(line.item_no)" class="flex justify-between">

                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.item_no }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.item_description }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.barcode }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.assembled_qty }}
                                                                </td>

                                                                <!-- <td class="px-3 py-2 text-xs">
                                                                    {{ line.prepacked_qty}}
                                                                </td> -->

                                                                <!-- <td class="px-3 py-2 text-xs text-center text-black bg-yellow-300 rounded-sm">
                                                                    <input
                                                                      class="text-center rounded"
                                                                      v-model="order.ass_qty"
                                                                      :disabled="order.prepacked_qty==order.total_order_qty"
                                                                    />
                                                                </td> -->
                                                                </div>
                                                            </tr>


                                                        </tbody>
                                                    </table>
                                                 <!-- <ul>
                                                    <li class="p-3" v-for="item in assembledArray">{{ item }}</li>
                                                 </ul> -->

                                                </div>
                                            </div>
                                            <Toolbar>
                                                <template #center>
                                                    <!-- <Pagination :links="orderLines.meta.links" /> -->

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
        <div v-show="showModal">
  <Modal :show="showModal" @close="showModal=false" :errors="errors"> <!-- {{ dynamicModalContent  }} -->
     <!-- {{ showModal }} -->

     <div class="p-4 font-bold text-center text-white bg-slate-600"> Assembly</div>
       <div>


        <form @submit.prevent="submitForm()"

        class="flex flex-col justify-center gap-2 p-5">
        <span class="p-3 text-center capitalize">{{ form.item_description }}</span>
        <div class="flex flex-row justify_between ">

            <span class="px-3 text-center capitalize">Ordered Qty</span>
            <span class="px-3 text-center capitalize">{{ form.total_order_qty }}</span>
        </div>
        <div class="flex flex-row justify_between">

            <span class="px-3 text-center capitalize">Prepacked Qty</span>
            <span class="px-3 text-center capitalize">{{ form.prepacked_qty }}</span>
        </div>


           <InputText
             ref="scanItem"
             v-model="form.assembled_qty"
             :placeholder="form.assembled_qty"
           />
           <InputText

             v-model="form.batch_no"
             placeholder="Batch Nos."
           />
            <!-- <input v-model="form.shp_date" placeholder="Shipment Date" type="date"/> -->
            <!-- {{currentItem}} -->
            <Button  label="Assemble" icon="pi pi-send" class="w-sm" severity="success"  type="submit" :disabled="form.processing" />
            <Button label="Cancel" severity="danger" icon="pi pi-cancel" @click="showModal=false"/>


        </form>


     </div>



  </Modal>
        </div>

    </template>
    <style>
    button:hover {
        cursor: pointer;
    }

    p:hover {
        cursor: pointer;
    }
</style>
