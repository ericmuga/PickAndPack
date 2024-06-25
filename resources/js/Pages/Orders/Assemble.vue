<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted,computed} from 'vue';

import { Inertia } from '@inertiajs/inertia';
 import Modal from '@/Components/Modal.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
// import { pipe } from 'gsap-trial/all';
const search=ref('')
const inputField=ref(null);
const showModal=ref(false);

const props=defineProps({
    orders:Object,
    picks:Object,
})

const pickArray= ref([]);

const ordersArray=ref([]);
const addToPick=(order_no)=>{
    pickArray.value=pickArray.value.filter(item=>item!==order_no)
    pickArray.value.push(order_no)
}

watch(pickArray, async ()=>{
 await fetchLines()

},{deep:true});


const lineFetch=ref(false);
const ordersInPicks=ref([]);

onMounted(()=>{
    inputField.value.focus();


     const filteredOrders = props.orders.filter(order => order.Complete==0);

            ordersArray.value.push(...filteredOrders);
     ordersInPicks.value=props.picks
        });

const hasA = computed(() => ordersArray.value.some(order => order.A_Assignment_Count === "1"));
const hasB = computed(() => ordersArray.value.some(order => order.B_Assignment_Count === "1"));
const hasC = computed(() => ordersArray.value.some(order => order.C_Assignment_Count === "1"));
const hasD = computed(() => ordersArray.value.some(order => order.D_Assignment_Count === "1"));







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

    const orderParts=ref([]);
    const currentPart=ref();

   const combinationExists=(order_no, part, array)=> {
        return array.some(item => item.order_no === order_no && item.part === part);
        }

   const showPickModal = (part) =>
   {
        pickLines.value=[];
        pickArray.value=[];
        orderParts.value = ordersArray.value.filter(order => !combinationExists(order.order_no, part, ordersInPicks.value))
                                            .filter(order => order[`${part}_Assignment_Count`] === '1')
                                            .reduce((acc, obj) => {
                                                const key = obj.sp_name;
                                                if (!acc[key]) {
                                                    acc[key] = [];
                                                }
                                                acc[key].push(obj);
                                                return acc;
                                                }, {});
        showModal.value = true;
        currentPart.value=part;

   };

   const pickLines=ref([])


 const fetchLines = async () => {
  // Show the loading alert
  Swal.fire({
    title: 'Loading',
    text: 'Please wait...',
    allowOutsideClick: false,
    allowEscapeKey: false,
    allowEnterKey: false,
    didOpen: () => {
      Swal.showLoading()
    }
  })

  try {
    // Make the GET request
    if (pickArray.value.length>0)
    {
         axios.post(route('fetchPickLines',{ pickOrders:pickArray.value,part:currentPart.value}))
         .then(res=>{
            //push to pickLies
              pickLines.value=res.data

        })
        .catch(error=>{
            Swal.fire('Error',`An error occurred when retrieving the item lines: ${error.message}`,'error')

        })
              lineFetch.value=false;
    }
    else{
        pickLines.value=[];
    }
    lineFetch.value=true;


    Swal.close()
  } catch (error) {
    // Close the loading alert
    Swal.close()

    // Handle the error
    console.error(error)
    Swal.fire({
      title: 'Error',
      text: 'There was an error with the request.',
      icon: 'error',
    })
  }
}


//    const fetchLines=()=>{

//     if (pickArray.value.length>0)
//     {
//          axios.post(route('fetchPickLines',{ pickOrders:pickArray.value,part:currentPart.value}))
//          .then(res=>{
//             //push to pickLies
//               pickLines.value=res.data

//         })
//         .catch(error=>{
//             Swal.fire('Error',`An error occurred when retrieving the item lines: ${error.message}`,'error')

//         })
//               lineFetch.value=false;
//     }
//     else{
//         pickLines.value=[];
//     }
// lineFetch.value=true;

//    }

const groupedByItem = computed(() => {
  return pickLines.value.reduce((acc, item) => {
    if (acc[item.item_no]) {
      acc[item.item_no].quantity += parseFloat(item.order_qty);
    } else {
      acc[item.item_no] = {
        description: item.item_description,
        quantity: parseFloat(item.order_qty)
      };
    }
    return acc;
  }, {});
});

const orderCount = computed(() => {
  const orderNumbers = new Set(pickLines.value.map(item => item.order_no));
  return orderNumbers.size;
});


const confirmPick= async () =>{

     await axios.post(route('createPick'),{pickOrders:pickArray.value,part:currentPart.value})
                .then(res=>{
                        //redirect to pick assembly
                        Swal.fire('Success!',`Pick : ${res.data.pick_id} created successfully`,'success');
                         for(order in orderParts)
                         {
                            ordersInPicks.value.push({order_no:order,part:currentPart});
                         }
                     })

}

const checkOrderInPicks=(order_no,part)=>{
    const exists=ordersInPicks.value.filter(item=>item.order_no==order_no && item.part==part)
    if (exists && exists.length>0) return true;
    return false;
}


</script>


<template>
    <Head title="Assembly"/>

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
                                    <div class="text-center">
                                        <!-- <Pagination :links="orderLines.meta.links" /> -->
                                         <div class="flex flex-row items-center gap-2 p-1 text-center ">

                                        <Button
                                         label="Pick A"
                                         @click="showPickModal('A')"
                                         v-show="hasA"
                                        />

                                        <Button
                                         label="Pick B"
                                         @click="showPickModal('B')"
                                         v-show="hasB"
                                        />

                                        <Button
                                         label="Pick C"
                                         @click="showPickModal('C')"
                                         v-show="hasC"
                                        />
                                        <Button
                                         label="Pick D"
                                         @click="showPickModal('D')"
                                         v-show="hasD"
                                        />
                                        </div>
                                        <input  type="text" v-model="search"
                                                ref="inputField"
                                                placeholder="Search Order"
                                                class="m-2 text-center rounded-lg bg-slate-300 text-md"
                                        />



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
                                                        :disabled="checkOrderInPicks(order.order_no,'A')"
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
                                                        :disabled="checkOrderInPicks(order.order_no,'B')"
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
                                                        :disabled="checkOrderInPicks(order.order_no,'C')"
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
                                                        :disabled="checkOrderInPicks(order.order_no,'A')"
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

    <Modal :show="showModal" @close="showModal=false" >

        <div class="p-5 ">

            <!-- <ul> -->
                <!-- <li v-for="order in orderParts" :key="order.order_no" class="flex flex-row justify-between py-3 hover:bg-slate-300"
                >
                    {{ order.order_no +'|'+ order.shp_name }}
                    <input type="checkbox" :value="order.order_no" v-model="pickArray" />
                </li> -->
                 <div style="height:350px" class="overflow-y-auto">
                    <div v-for="(orders, spName) in orderParts" :key="spName" >
                        <h2 class="p-1 font-bold rounded-md bg-slate-300">{{ spName }}:</h2>
                        <div v-for="order in orders" :key="order.order_no" class="flex flex-row justify-between p-1 py-3 order hover:bg-slate-300 " >
                                   {{ order.order_no +'|'+ order.shp_name }}
                            <input type="checkbox" :value="order.order_no" v-model="pickArray" />
                        </div>
                        <hr>
                  </div>
                 </div>
                    <!-- <div class="w-full text-center">
                    <Button @click="confirmPick()"
                            severity="warning"
                            v-show="pickLines.length==0"
                            :disabled="lineFetch||pickArray.length==0"
                            label="Create Pick"
                            />
                  </div> -->
                  <div style="height:350px" class="overflow-y-auto">
                 <hr class="w-full p-3"/>
                  <h2 class="w-full p-3 font-bold text-center" v-show="pickLines.length>0">Pick-{{currentPart}}</h2>
                    <div v-for="(item, itemNo) in groupedByItem" :key="itemNo" class="flex flex-row justify-between w-full ">
                           <div class="justify-start">{{ itemNo }} - {{ item.description }}</div>
                           <div class="justify-end font-bold">{{ item.quantity }}</div>
                    </div>

                    <h2 class="w-full p-3 font-bold text-center" v-show="pickLines.length>0">
                        Order Count:{{ orderCount }}
                        <ul>
                            <li v-for="line in pickArray">
                                {{ line }}
                            </li>
                        </ul>

                    </h2>
                    <div class="p-3 text-center">
                        <Button severity="success" v-show="pickLines.length>0" label="Confirm" @click="confirmPick()" icon="pi pi-print"/>
                    </div>
              </div>

            <!-- </ul> -->

        </div>


    </Modal>
</template>
