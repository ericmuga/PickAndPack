<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import StatsTile from '@/Components/StatsTile.vue';
import { Link } from '@inertiajs/inertia-vue3'
import { useForm } from '@inertiajs/inertia-vue3';
import { watch,ref, onMounted, reactive } from 'vue';
// import {InputText} from 'primevue/inputtext'
import InputText from 'primevue/inputtext';;
import Dropdown from 'primevue/dropdown';
import  debounce  from "lodash/debounce";
import {Inertia} from '@inertiajs/inertia';

import { StreamBarcodeReader } from "vue-barcode-reader";
import SelectButton from 'primevue/selectbutton';
import ToggleButton from 'primevue/togglebutton';
// import func from 'vue-temp/vue-editor-bridge';
// import { ImageBarcodeReader } from "vue-barcode-reader";

let form =useForm({ camera:'On' , orderId:'', counter:0});

let counter=reactive(0);

// const props=defineProps({
//     pendingOrders:Object,
// });


// let  value1='On';
let options= [{name:'Off'},{name :'On'}];

// let Orders=props.pendingOrders.map((el)=>el.OrderNo+'|'+el.Customer+'|'+el.ShipTo)
// let toggle=()=>{ if(value1==='Off') value1='On'; else value1='Off'}
// let checked1=false;
//  checked2: true

// let orderId=reactive('')

let playSound=(result)=>{

    var audio = new Audio(beep.soundurl);
    //   alert(result)
    // document.getElementById('#oderId').innerHTML.value=result;
    // orderId=result.value
    audio.play();
    //return result

    // Inertia.get(route('order.show',{'id':value}),{},{preserveState:true,replace:true})

}

let beep={ soundurl : '/sound/beep-07a.mp3'}


let onDecode = (result)=>{
    //alert(result)
    //   console.log(result);
    // let res= result.toString();
    // console.log(res);
    // form.orderId=result


     playSound(result);

      form.counter++;
    // console.log(form.counter)
    //alert(JSON.stringify(props.pendingOrders))
    // scannedOrder=props.pendingOrders[props.pendingOrders.map((el)=>el.No).indexOf(result)]
    //let noArray=props.pendingOrders.map((el)=>el.No)

    //console.log(props.pendingOrders.map(el=>el.No))

    // console.log(props.pendingOrders.filter((el)=>el.No==result)[0])
    //    alert(props.pendingOrders.filter((el)=>{el.No!=result}))
    // scannedOrder={... props.pendingOrders.filter((el)=>el.No==result)[0]}
    // alert(scannedOrder)
    // form.camera='Off'


}

// let scannedOrder=reactive('')

// watch(orderId,debounce((value)=>{Inertia.get(route('order.show',{'id':value}),{},{preserveState:true,replace:true})},5));
// watch( () =>form.orderId,

// (value)=>{ console.log(form.orderId);
//     form.camera='Off';
//     scannedOrder={... form.orderId}
// }
// );
</script>

<template>
    <Head title="ScanItem" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Confirm Assembly</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->
                        <!-- <div class=" md:grid md:grid-cols-3 md:space-x-1 w-full items-center justify-between md:gap-1  sm:space-y-2 md:flex-row"> -->

                           <div class="grid md:grid-cols-3 sm:grid-cols-1">
                            <div class="flex flex-col items-center justify-center w-full col-span-1">

                                <div class=" flex-1 tracking-wide strong">Scan items</div>

                                <!-- <label for=""> Camera</label> -->

                                <!-- <h5 id="single">Single Selection</h5> -->
                                <!-- <form action=""  @submit.prevent="">
                                    <Dropdown v-model="form.camera" :options="options" optionLabel="name" optionValue="name" placeholder="Camera" />
                                </form> -->

                                <!-- <SelectButton v-model="value1" :options="options"  aria-labelledby="single" /> -->
                                <!-- <ToggleButton v-model="checked1" onIcon="pi pi-check" offIcon="pi pi-times" class="w-full sm:w-10rem" aria-label="Confirmation" /> -->
                                <!-- <div v-if="form.camera==='Off'">
                                    <InputText type="text" v-model="orderId" placeholder="Search"  />
                                    <Dropdown v-model="form.orderId"
                                    :options="pendingOrders"
                                    optionLabel="SearchName" :filter="true"
                                    placeholder="Select Order"
                                    :showClear="true"
                                    :optionValue="No"
                                    />

                                </div> -->

                                <!-- <div v-else> -->
                                    <StreamBarcodeReader @decode="onDecode" @loaded="onLoaded"  />
                                <!-- </div> -->

                                <!-- <div v-if="scannedOrder.length!=0 && form.camera==='Off'"> -->

                                    This is where the listing will come
                                    <!-- <div class="flex justify-between flex-col   mt-5">
                                        <tr class="flex justify-between text center border  hover:shadow-md">
                                            <th class="bg-teal-400 text-white px-1">Order No.</th><td class="px-1"> {{ scannedOrder.No }}</td>
                                        </tr>
                                        <tr class="flex justify-between text center border  hover:shadow-md">
                                            <th class="bg-teal-400 text-white px-1">Customer</th><td class="px-1"> {{ scannedOrder.Customer }}</td>
                                        </tr>
                                        <tr class="flex justify-between text center border  hover:shadow-md">
                                            <th class="bg-teal-400 text-white px-1">Address</th><td class="px-1"> {{ scannedOrder.ShipTo }}</td>
                                        </tr>

                                        <tr class="flex justify-between text center border  hover:shadow-md">
                                            <th class="bg-teal-400 text-white px-1">Route</th><td class="px-1"> Westlands</td>
                                        </tr>
                                        <tr class="flex justify-between text center border  hover:shadow-md">
                                            <th class="bg-teal-400 text-white px-1">Salesperson</th><td class="px-1"> 012</td>
                                        </tr>

                                    </div> -->
                                    <!-- display scanned/selected order details here-->


                                <!-- </div> -->


                                <!-- </div> -->
                                <!-- <ImageBarcodeReader @decode="onDecode" @error="onError"></ImageBarcodeReader> -->


                            </div>

                             <div  class="flex flex-col items-center justify-center w-full col-span-1 gap-y-2">
                                <div>
                                    Executed Quantity : 20
                                </div>
                                </div>
                            <div  class="flex flex-col items-center justify-center w-full col-span-1 gap-y-2 ">
                                   Confirmed Quantity
                                     <div class="font-xl">
                                        <InputText v-model="form.counter" />
                                     </div>




                            </div>
                            <!--end of stats bar-->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </AuthenticatedLayout>

    </template>

    <style scoped>

    </style>
