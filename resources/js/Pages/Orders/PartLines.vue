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


// import { ref, onMounted } from 'vue';
import { ProductService } from '@/service/ProductService'
import { useOrderStore } from '@/service/OrderStore'
// import { useConfirm } from "primevue/useconfirm";

// const confirm = useConfirm();

const form2= useForm({
                      'prepack_name':'',
                      'quantity':0,
                      'order_no':'',
                      'line_no':0
                    //   'totalQuantity':0

                    });

let currentPrepacks=ref([]);
let prepacksAvailable=ref('');
let currentLineNo=ref('');
let currentOrderNo=ref('');



const products = ref(null);
// const orders=ref(null);
onMounted(() => {

    // ProductService.getProductsSmall().then((data) => (products.value = [data, []]));
    // let orderStore= useOrderStore();

    // orderStore.getOrders();
    // orders.value =orderStore.orders;



});

let dynamicModalContent=ref('No Prepacks for this line');

const generateModalContent=(order)=>
{
    // alert(showModal.value)
    // console.log(order)
     prepacksAvailable=order.prepacks_available
     currentPrepacks=order.prepacks
     currentOrderNo=order.order_no
     currentLineNo=order.line_no

     showModal.value=true
   // Inertia.get('line/prepacks',{'line_no':line_no})



}
const add=()=>{
    form2.line_no=currentLineNo
    form2.order_no=currentOrderNo
    console.log(form2)
    form2.post(route('prepacks.add'))
    showModal.value=false
}
// const part =ref();
let showModal=ref(false);
const parts= ref([
                {'name':'A','code':'A'},
                {'name':'B','code':'B'},
                {'name':'C','code':'C'},
                {'name':'D','code':'D'},
                {'name':'All','code':'All'},
])

// const sector =ref();

const sectors= ref([
                {'name':'RETAIL','code':'RETAIL'},
                {'name':'FAST FOOD','code':'FAST FOOD'},
                {'name':'HORECA','code':'HORECA'},
                {'name':'STAFF','code':'STAFF'},
                {'name':'BLANK','code':'BLANK'},
                {'name':'All','code':'All'},
])

// const items= ref([
//                     {'name':'Beef Smokies Labless 1Kg','code':'J31031702'},
//                     {'name':'Beef Sausage Catering 1Kg','code':'J31015501'},
//                     {'name':'All','code':'All'},

//                 ])

// watch(part, debounce(()=>{ Inertia.get('', {preserveScroll: true})}, 500));
// watch(sector, debounce(()=>{ Inertia.get(route('orders.assemble'),{part:part.value,sector:sector.value}, {preserveScroll: true})}, 500));


const form=useForm({
    sector:props.previousInput.sector,
    part:props.previousInput.part,
    spcode:props.previousInput.spcode,
    item:props.previousInput.item
})


const props= defineProps({
    orderLines:Object,
    selectedPart:Object,
    previousInput:Object,
    items:Object
    // printed:Object
})

// const =()=>{form.get(route('orders.lines'))}

const submitForm=()=>{ form.post(route('orders.prepack'))}

// const pp= ()=>{ showModal=!showModal;}
</script>

<template>
    <Head title="Orders"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Parts</h2>
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
                                        <Pagination :links="orderLines.meta.links" />
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

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
  <div class="flex flex-row w-full ">

        <!-- <a href="/orders/download" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="confirmations"/>
                                        </a> -->

  <!-- <Button type="button" rounded  label="Pre-pack" outlined @click="prepack()"  /> -->
  <!-- <Button type="button" rounded  label="Pre-pack" outlined @click="pp()"  /> -->

   <Button

                      label="Prepack"
                      @click="showModal=true"
                    />

  <Button type="button" rounded disabled label="Total Lines"  :badge=props.orderLines.meta.total badgeClass="p-badge-danger" outlined class="justify-end" />
    </div>

<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

        <tr class="bg-slate-300">
            <th  scope="col" class="px-6 py-3">Part</th>
            <th  scope="col" class="px-6 py-3">Order No.</th>
            <th  scope="col" class="px-6 py-3">Customer</th>
            <th  scope="col" class="px-6 py-3">Ship-to</th>
            <th  scope="col" class="px-6 py-3">Saleperson</th>
            <th  scope="col" class="px-6 py-3">Item</th>
            <th  scope="col" class="px-6 py-3">Description</th>
            <th  scope="col" class="px-6 py-3">Ordered qty</th>
            <th  scope="col" class="px-6 py-3"> Prepack</th>
            <th  scope="col" class="px-6 py-3">Assembled qty</th>
            <th  scope="col" class="px-6 py-3">Customer Spec</th>
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
                    {{ order.customer_name }}
                </td>
                <td class="px-3 py-2 text-xs">
                    {{ order.shp_name }}
                </td>
                <td class="px-3 py-2 text-xs text-center">
                    {{ order.sp_code }}
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
    </TabPanel>


    <TabPanel header="Consolidate">
        <div>
           <PickList v-model="orders" listStyle="height:342px" dataKey="id">
            <template #sourceheader> Available </template>
            <template #targetheader> Selected </template>
            <template #item="slotProps">
                <div class="flex flex-wrap gap-3 p-2 align-items-center">
                    <!-- <img class="flex-shrink-0 w-4rem shadow-2 border-round" :src="'https://primefaces.org/cdn/primevue/images/product/' + slotProps.item.image" :alt="slotProps.item.name" /> -->
                    <div class="flex flex-1 gap-2 flex-column">
                        <span class="font-bold">{{ slotProps.item.order_no }}</span>
                        <div class="flex gap-2 align-items-center">
                            <i class="text-sm pi pi-tag"></i>
                            <span>{{ slotProps.item.customer_name }}</span>
                        </div>
                    </div>
                    <span class="font-bold text-900">$ {{ slotProps.item.shp_name }}</span>
                </div>
            </template>
        </PickList>
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

<Modal :show="showModal" @close="showModal=false" :errors="errors">
     <!-- {{ dynamicModalContent  }} -->

     <div class="w-full p-4 font-bold text-center text-white bg-slate-600"> Make Prepack</div>
       <div >


        <form @submit.prevent="submitForm()" class="flex flex-col justify-center gap-2 p-5">
            <Dropdown v-model="form.part" :options="parts" optionLabel="name" editable="" optionValue="code" placeholder="Select Part" class="" />
            <Dropdown v-model="form.sector" :options="sectors" optionLabel="name" editable="" optionValue="code" placeholder="Select Sector"  />
            <Dropdown v-model="form.item" :options="props.items" optionLabel="description" editable="" optionValue="item_no" placeholder="Prepack Item" class="" />
            <InputText v-model="form.spcode" placeholder="Salesperson Code"></InputText>
            <Button  label="Create" severity="success"  type="submit" :disabled="form.processing" />

        </form>



       <!-- <form @submit.prevent="add()">

          <Dropdown
              v-model="form2.prepack_name"
              :options="prepacksAvailable"
              optionLabel="name"
              optionValue="name"
              filter
              placeholder="Select a Prepack Size"
              class="w-full md:w-14rem"
           />

           <InputNumber
             v-model="form2.quantity"
             placeholder="No. of Prepacks"
           />


           <Button
             type="submit"
             label="Add"
           />



           <table v-show="currentPrepacks.length!=0">
               <tr v-for="prepackLine in currentPrepacks" id="prepackLine.id" >
                <td>{{ prepackLine.prepack_name }}</td>
                <td>{{ prepackLine.prepack_count }}</td>

               </tr>
           </table>

       </form> -->
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
