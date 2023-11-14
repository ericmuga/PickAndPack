

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { onMounted,ref,reactive,watch,computed,onUnmounted } from 'vue';
import SearchBox from '@/Components/SearchBox.vue'
import debounce from 'lodash/debounce'
import ProgressBar from 'primevue/progressbar';

import { useSearchArray } from '@/Composables/useSearchArray';
import Swal from 'sweetalert2'
import { Inertia } from '@inertiajs/inertia';

const inputField=ref(null);


const props= defineProps({
    vessels:Object,
    session:Object,
    // pick_no:String,

})


onMounted(() => {
    inputField.value.focus();

    //populate assembled
    //for each line, get the last assembly push that into the assembled array




//     setInterval(() => {
//         if (!assembledArray.value.length==0 && isRunning.value==true)
//            Inertia.post(route('assembly.store'),{'data':assembledArray.value,
//                                                     'autosave':true,
//                                                     'assembly_time':formatTime.value
//                                                 },{preserveScroll:true,preserveState:true}
//                                                 )

// }, 60000);

});


const manualScan=(item)=>{newItem.value=''; newItem.value=item;};
const newItem = ref('');

let scanError = ref('');
const isActive = ref(false);

const extractedData = ref(Object.entries(props.vessels.data).map(([key, value]) => {
//  console.log(value.qr_code)
    return {

        'vessel_type': value.vessel_type ,// Use the value with ref/ Extract 'age' key as value with ref
        'vessel_no': value.vessel_no ,// Use the value with ref/ Extract 'age' key as value with ref
        'qr_code': value.qr_code ,// Use the value with ref/ Extract 'age' key as value with ref
        'part':value.part,
        'order_no':value.order_no,
        'shp_name':value.shp_name,


    };


}));

const searchKey = ref('');
const searchValue = ref('');
const {searchByQrCode,searchByMultipleKeyValues } = useSearchArray(extractedData)
const searchResult = ref(0);

const assembledArray=ref([]);



watch( newItem,
 debounce(
            function () {
                startTimer();
                if (newItem.value.trim()!='' ){
                            scanError.value = '';

                       searchResult.value= searchByQrCode(newItem.value)
                       if (searchResult.value!=0)
                        {
                            // console.log(searchResult.value)
                           updateScannedItem(searchResult.value)
                        }
                        else
                          scanError.value=`The Vessel Does not belong to this loading session!`;
                    }
                      newItem.value=''
                        }
            ,300)

        );







const ItemInAssembledArray=(qr_code)=>{
   const existingItemIndex= assembledArray.value.findIndex(item => item.qr_code === qr_code)
   return(existingItemIndex!==-1)

}

const updateScannedItem =(form)=>{


    const existingItemIndex = assembledArray.value.findIndex(item => item.qr_code === form.qr_code);




    if (existingItemIndex !== -1)
    {
      // If the key already exists, update the value
      //   alert('here')
      assembledArray.value[existingItemIndex].qr_code = form.qr_code;
      assembledArray.value[existingItemIndex].part = form.part;
      assembledArray.value[existingItemIndex].shp_name = form.shp_name;
      assembledArray.value[existingItemIndex].vessel_no = form.vessel_no;
      assembledArray.value[existingItemIndex].vessel_type = form.vessel_type;
    //   assembledArray.value[existingItemIndex].to_batch = form.to_batch;
      // assembledArray.value[existingItemIndex].qty_base = form.qty_base;
    } else
    {
      // If the key doesn't exist, push a new key-value pair
      assembledArray.value.push({ 'qr_code':form.qr_code,
                                   'part':form.part,
                                   'shp_name':form.shp_name,
                                   'vessel_no':form.vessel_no,
                                   'vessel_type':form.vessel_type
                                 });
    }




}

const popVessel=(form)=>{
     assembledArray.value.pop({ 'qr_code':form.qr_code,
                                   'part':form.part,
                                   'shp_name':form.shp_name,
                                   'vessel_no':form.vessel_no,
                                   'vessel_type':form.vessel_type
                                 });
    };



const closeAssembly=()=>{

     //if the assembled quantity is not equal to the ordered quantity



     Swal.fire({
                                        title: 'Are you sure?',
                                        text: " Loaded orders may not be undone!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Close Loading!'
                                        }).then((result) => {
                                            stopTimer();
                                            if (result.isConfirmed) {Inertia.post(route('load.add'),{'data':assembledArray.value,
                                                                                                            'autosave':false,
                                                                                                         'loading_time':formatTime.value,
                                                                                                        'status':'complete',
                                                                                                        'session_id':props.session.data.id
                                                                                                        });
                                                                    }
                                                           })
}




let autosave=ref(false);

const isRunning = ref(false);
const startTime = ref(0);
const currentTime = ref(0);

const formatTime = computed(() => {
  const totalMilliseconds = currentTime.value;
  const milliseconds = totalMilliseconds % 1000;
  const totalSeconds = (totalMilliseconds - milliseconds) / 1000;
  const seconds = totalSeconds % 60;
  const totalMinutes = (totalSeconds - seconds) / 60;
  const minutes = totalMinutes % 60;
  const hours = (totalMinutes - minutes) / 60;

  const formatMilliseconds = milliseconds.toString().padStart(3, '0');
  const formatSeconds = seconds.toString().padStart(2, '0');
  const formatMinutes = minutes.toString().padStart(2, '0');
  const formatHours = hours.toString().padStart(2, '0');

  return `${formatHours}:${formatMinutes}:${formatSeconds}.${formatMilliseconds}`;
});


const startTimer = () => {
  if (!isRunning.value) {
    startTime.value = Date.now() - currentTime.value;
    isRunning.value = true;
  }
};

const stopTimer = () => {
  if (isRunning.value) {
    isRunning.value = false;
  }
};
const resetTimer = () => {
  if (!isRunning.value) {
    currentTime.value = 0;
  }
};

const timerInterval = setInterval(() => {
  if (isRunning.value) {
    currentTime.value = Date.now() - startTime.value;
  }
}, 100);

onUnmounted(() => {
  clearInterval(timerInterval);
});





</script>

<template>
 <Head title="Vessels"/>

    <AuthenticatedLayout>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Load</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>


                                <template #center>
                                    <div class="flex flex-row space-x-3">
                                        <span class="p-3 text-white rounded-lg bg-lime-500">{{ session.data.vehicle }}</span>
                                        <h2 class="flex flex-row text-xl font-bold tracking-wide text-red-500"> {{ formatTime }}
                                        {{ session.route }}
                                        </h2>
                                        <span class="p-3 text-white bg-indigo-700 rounded-lg">{{session.data.loader }}</span>
                                    </div>


                                </template>


                        </Toolbar>




                    </div>


         </div>
        </div>

       <div class="flex flex-col items-center justify-center w-full gap-1 text-center">

                                        <input type="text" v-model="newItem"  ref="inputField" placeholder="Scan Item" class="m-2 rounded-lg bg-slate-300 text-md">

                                                   <p v-if="scanError" class="p-3 m-3 font-bold text-black bg-red-400 rounded">{{ scanError }}</p>
                                        </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <div class="w-full m-2 text-center">


                                               {{assembledArray.length }} / {{ vessels.data.length }}

                                               <ProgressBar :value="Math.round((assembledArray.length)/(vessels.data.length)*100)" />

                                            </div>



                                            <div class="grid gap-3 sm:grid-cols-1 md:grid-cols-2 ">

                                                <div class="col-span-1">
                                                    <div  class="w-full p-3 m-2 font-bold text-center text-white bg-orange-700">Pending Loading</div>
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">

                                                        <tbody>
                                                            <tr
                                                              @click="newItem=line.qr_code"

                                                              v-for="line in vessels.data" :key="line.id"

                                                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-400 hover:text-white ">
                                                                <div v-if="!ItemInAssembledArray(line.qr_code)" class="flex justify-between">
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.part }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.vessel_type }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.vessel_no }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.shp_name }}
                                                                </td>

                                                                </div>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-span-1"  >

                                             <div  class="w-full p-3 m-2 font-bold text-center text-black bg-lime-400"> Loaded</div>
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                                                                                                               <tbody>

                                                            <tr v-for="line in assembledArray" :key="line.id"
                                                            @click="popVessel(line)"

                                                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-400 hover:text-white ">

                                                                <div v-if="ItemInAssembledArray(line.qr_code)" class="flex justify-between">

                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.part }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.vessel_type }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.vessel_no }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.shp_name }}
                                                                </td>

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
                                                    <!-- <Pagination :links="vessels.meta.links" /> -->
                                                     <Button
                                                            class="justify-end"
                                                            icon="pi pi-check"
                                                        label="Load"
                                                        severity="success"
                                                        @click="closeAssembly()"



                                                />

                                                </template>
                                            </Toolbar>
                                        </div>



</div>
    </div>
    </AuthenticatedLayout>
</template>



<style lang="scss" scoped>

</style>
