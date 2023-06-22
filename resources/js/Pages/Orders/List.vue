<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
// import Button from 'primevue/button';
// import MultiSelect from 'primevue/multiselect';
// import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref} from 'vue';
import Pagination from '@/Components/Pagination.vue'
// import Swal from 'sweetalert2'
// import FilterPane from '@/Components/FilterPane.vue'
import Modal from '@/Components/Modal.vue'
import { useStorage } from '@/Composables/useStorage';
import { useDates } from '@/Composables/useDates';


// const todaysDate=useDates.getCurrentDate();

const form = useForm({
    search: null,

})

let showModal=ref(false);

const showFilterPane=()=>{showModal=true;}

const search=ref()
watch(search, debounce(()=>{Inertia.get('/all',{search:search.value}, {preserveScroll: true})}, 500));
// watch(form.searchKey, debounce(form.post('all',{preserveScroll:true}),600))

const form2 =useForm({
    Part:String,
    Order:String
});

const selectedParts =ref([])

const props= defineProps({
    orders:Object,
    printed:Object,
    columnListing:Object
})

watch(selectedParts,console.log(selectedParts));

const  updateCheckedItems=()=>{
    selectedParts.value = selectedParts.value.filter(item => items.some(i => i.id === item));
}



//write to local storage
const write=()=>{}
// const search=()=>

const checkPart=(Order,Part)=>{
    //update the status of the part to printed, and log who printed and what time
    // alert(Order+Part)
    form2.Part=Part
    form2.Order=Order
    form2.post('/updatePart',{preserveScroll:true});

}
const CheckPrinted=(Order,Part)=>{
    props.printed
}

const ConfirmPrint=(order_no,part_no)=>{
    Inertia.post(route('order.confirm'),{order_no,part_no},{preserveScroll:true});
}

const dynamicObject=ref({});
// const dateRef =ref({})

Object.keys(props.columnListing).forEach(key => {
  dynamicObject.value[key] = ref('');

});

function getKeysWithDateType(obj) {
  const columnListing = obj.columnListing;

  if (typeof columnListing !== "object" || columnListing === null) {
    return [];
  }

  return Object.keys(columnListing).filter(
    (key) => columnListing[key].type === "date"
  );
}


function removeNullAndBlankKeys(obj) {
  return Object.fromEntries(
    Object.entries(obj).filter(([_, value]) => value !== null && value !== '')
  );
}
const dateKeys = getKeysWithDateType(props);
console.log(dateKeys);


const dateDynamicObject = dateKeys.map((key) => ({
  from: null,
  to: null,
  id: key,
}));


function transformObjects(inputObj) {
  function transformObject(inputItem) {
    return {
      from: inputItem.from,
      to: inputItem.to
    };
  }

  const transformedObj = {};

  for (const key in inputObj) {
    if (inputObj.hasOwnProperty(key)) {
      const inputObjItem = inputObj[key];
      const transformedItem = transformObject(inputObjItem);
      transformedObj[inputObjItem.id] = transformedItem;
    }
  }

  return transformedObj;
}

const postForm=(dynamicObject,dateDynamicObject)=>{

    // showModal=false
    Inertia.post(route('order.filter',{...removeNullAndBlankKeys(dynamicObject),...removeNullAndBlankKeys(transformObjects(dateDynamicObject))}))
        //    .onSuccess(showModal=false)
}

// console.log(dateDynamicObject);
 // Output: ['column3', 'column4']
</script>

<template>
    <Head title="Orders"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Orders {{ }}</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>
                                    <!-- <Button label="New" icon="pi pi-plus" class="mr-2" />
                                        <Button label="Upload" icon="pi pi-upload" class="p-button-success" /> -->
                                        <!-- <i class="mr-2 pi pi-bars p-toolbar-separator" /> -->
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="items" class="p-button-warning"></SplitButton> -->

                                </template>
                                <template #center>
                                    <div>
                                        <Pagination :links="orders.meta.links" />
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>
                                        <a href="/orders/download" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="confirmations"/>
                                        </a>
                                         <!-- <Link :href="route('refresh')" class="w-20 h-20 m-5 mx-auto text-center ">
                                            <img src="/img/refresh.png" />
                                            <Button icon="pi pi-heart" severity="help" rounded aria-label="Favorite" />
                                            <Button icon="pi pi-refresh" severity="primary" rounded />
                                            <img src="/img/scanner.jpg" />
                                        </Link>
                                         -->
                                    <Button
                                         label="Filters"
                                         @click="showModal=true"
                                         rounded
                                    ></Button>


                                            <InputText v-model="search" aria-placeholder="search"/>

                                            </template>
                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                            Order No.
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Sales Person
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Ship-to Name
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Shipment Date
                                                        </th>

                                                        <th scope="col" class="px-6 py-3">
                                                            Printing Time
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Printed By
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Printing Date
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part A Items
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part B Items
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part C Items
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Part D Items
                                                        </th>


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="order in orders.data" :key="order.order_no"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ order.order_no }}
                                                    </td>
                                                    <td class="flex flex-col px-3 py-2 text-xs text-center">
                                                        <span class="text-xs font-bold">{{order.sp_code}}</span>
                                                        <span class="text-xs font-thin">{{order.sp_name}}</span>
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center capitalize bg-yellow-200 rounded-full">
                                                        {{ order.shp_name }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ order.shp_date }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs">

                                                            {{order.ending_time}}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">
                                                        {{ order.ended_by}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs">
                                                        {{      order.ending_date}}

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_a!=0">

                                                        <Button v-show="order.confirm_a" icon="pi pi-check" severity="info" rounded :label="order.part_a" disabled />

                                                        <Button  v-show="!order.confirm_a" icon="pi pi-bell" severity="warning" :badge=order.part_a text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'A')"/>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_b!=0">
                                                        <Button v-show="order.confirm_b" icon="pi pi-check" severity="info" rounded :label="order.part_b" disabled />

                                                        <Button  v-show="!order.confirm_b" icon="pi pi-bell" severity="warning" :badge=order.part_b text raised rounded aria-label="Notification" @click="
                                                        ConfirmPrint(order.order_no,'B')"/>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_c!=0">
                                                        <Button v-show="order.confirm_c" icon="pi pi-check" severity="info" rounded :label="order.part_c" disabled />

                                                        <Button  v-show="!order.confirm_c" icon="pi pi-bell" severity="warning" :badge=order.part_c text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'C')"/>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                                    <td class="p-1 px-3 py-2 text-xs text-center " v-if="order.part_d!=0">
                                                        <Button v-show="order.confirm_d" icon="pi pi-check" severity="info" rounded :label="order.part_d" disabled />

                                                        <Button  v-show="!order.confirm_d" icon="pi pi-bell" :badge=order.part_d severity="warning" text raised rounded aria-label="Notification" @click="ConfirmPrint(order.order_no,'D')"/>
                                                    </td>
                                                    <td v-else  class="bg-slat-200">

                                                    </td>
                                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <Pagination :links="orders.meta.links" />
                            </div>
                        </template>
                    </Toolbar>


                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" >

      <div class="grid p-2 m-4 place-items-center">
         <heading class="w-full p-4 tracking-wide text-black underline bg-slate-200 align-center">Filter Pane</heading>
         <form class="" @submit.prevent="postForm(dynamicObject,dateDynamicObject)">
            <div v-for="item in columnListing" :key="item.name">

                <div v-if="item.type==='string' && item.default_values.length==0" class="p-3">
                    <span class="p-float-label">
                        <InputText :id="item.name"  v-model="dynamicObject[item.name]" class="p-inputtext-sm"/>
                        <label :for="item.name" class="capitalize">{{ item.name }}</label>
                    </span>

                </div>
              <div v-else-if="item.type==='string' &&item.default_values.length>0">
                <Dropdown :options="item.default_values" v-model="dynamicObject[item.name]" />
             </div>

             <div v-if="dateDynamicObject.length!=0">





             </div>
            </div>
            <div v-for="dateData in dateDynamicObject" :key="dateData.id">
                    <label :for="dateData.id">{{ dateData.id }}</label>
                    <input type="date" v-model="dateData.from"/>
                    <input type="date" v-model="dateData.to"/>
                    <!-- <Calendar v-model="dateData.from" selectionMode="range"  :manualInput="true" /> -->
                </div>
        <div class="p-2 text-center">
                    <Button type="Submit" label="Search" />
                </div>
                </form>
      </div>


   <!-- <FilterPane :members="columnListing" :targetRoute="route('order.filter')"/> -->
  </Modal>
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
