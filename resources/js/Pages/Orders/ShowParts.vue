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

let form =useForm({ camera:'Off' , orderId:''});

const props=defineProps({
    order:Object,
});


// let  value1='On';
let options= [{name:'Off'},{name :'On'}];

// let Orders=props.pendingOrders.map((el)=>el.OrderNo+'|'+el.Customer+'|'+el.ShipTo)
// let toggle=()=>{ if(value1==='Off') value1='On'; else value1='Off'}
// let checked1=false;
//  checked2: true

let orderId=reactive('')

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
    //alert(JSON.stringify(props.pendingOrders))
    // scannedOrder=props.pendingOrders[props.pendingOrders.map((el)=>el.No).indexOf(result)]
    //let noArray=props.pendingOrders.map((el)=>el.No)

    //console.log(props.pendingOrders.map(el=>el.No))

    console.log(props.pendingOrders.filter((el)=>el.No==result)[0])
    //    alert(props.pendingOrders.filter((el)=>{el.No!=result}))
    scannedOrder={... props.pendingOrders.filter((el)=>el.No==result)[0]}
    // alert(scannedOrder)
    form.camera='Off'


}

let scannedOrder=reactive('')

// watch(orderId,debounce((value)=>{Inertia.get(route('order.show',{'id':value}),{},{preserveState:true,replace:true})},5));
watch( () =>form.orderId,

(value)=>{ console.log(form.orderId);
    form.camera='Off';
    scannedOrder={... form.orderId}
}
);
// watch(form.orderId,(value)=>alert(value));
// watch(form.orderId,debounce((value)=>{Inertia.get(route('order.show',{'id':value}),{},{preserveState:true,replace:true})},5));

</script>

<template>
    <Head title="ScanPart" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ order.No }}| {{ order.Customer }}</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">



                        <div class="flex flex-auto justify-center w-full p-3 m-2 shadow-sm border-2 flex-col gap-3 items-center">
                        <div v-for="item in order.parts" :key="item.name">
                           <Link
                             :href="`/order/${order.No}/${item.name}`">
                                <Button
                                    :label="`Part${item.name}`"
                                    :class="item.class"

                                />
                          </Link>



                        </div>

                        </div>

                     </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>

    <style scoped>

    </style>
