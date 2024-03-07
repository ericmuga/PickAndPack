<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import {watch, ref,onMounted,reactive,computed,onUnmounted} from 'vue';
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce'
import ProgressBar from 'primevue/progressbar';
import { useSearchArray } from '@/Composables/useSearchArray';

const inputField=ref(null);
const scanItem=ref(null);

const props= defineProps({
    orderLines:Object,


})


onMounted(() => {
    inputField.value.focus();

    //populate assembled
    //for each line, get the last assembly push that into the assembled array

    if (props.orderLines.length>0)

  {
   for (var i = props.orderLines.length - 1; i >= 0; i--)
   {




    for (var j = props.orderLines[i].assemblies.length - 1; j >= 0; j--)
    {
     const result = searchByMultipleKeyValues([
                                                  ['line_no', props.orderLines[i].assemblies[j].line_no],
                                                  ['order_no', props.orderLines[i].assemblies[j].order_no]

                                                ]);



      if (result.value!=0)
      {
        assembledArray.value.push({
                                   'item_no':result.item_no,
                                   'assembled_qty':result.assembled_qty,
                                    'assembled_pcs':result.assembled_pcs,
                                    'order_qty':result.order_qty,
                                //    'prepacks_total_quantity':result.prepacks_total_quantity,
                                   'item_description':result.item_description,
                                   'barcode':result.barcode,
                                    'order_no':result.order_no,
                                    'line_no':result.line_no,
                                     'from_batch':props.orderLines[i].assemblies[j].from_batch,
                                     'to_batch':props.orderLines[i].assemblies[j].to_batch,

                            });



      };


    };

};

};


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

        'order_qty': value.order_qty ,// Use the value with ref/ Extract 'age' key as value with ref
        'qty_base': value.qty_base ,// Use the value with ref/ Extract 'age' key as value with ref
        // 'prepacks_total_quantity': value.prepacks_total_quantity ,// Use the value with ref/ Extract 'age' key as value with ref
        'barcode':value.barcode,
        'item_no':value.item_no,
        'item_description':value.item_description,
        'order_no':value.order_no,
        'line_no':value.line_no

    };
}));

const searchKey = ref('');
const searchValue = ref('');
const {searchByBarcodeOrItemNo,searchByMultipleKeyValues } = useSearchArray(extractedData)
const searchResult = ref(0);

const assembledArray=ref([]);



watch( newItem,
 debounce(
            function () {
                startTimer();
                if (newItem.value.trim()!='' ){
                            scanError.value = '';

                       searchResult.value= searchByBarcodeOrItemNo((newItem.value.toUpperCase()).trim())
                       if (searchResult.value!=0)
                        {
                            //  alert('hey')
                            //if (parseFloat(searchResult.value.order_qty)>parseFloat(searchResult.value.prepacks_total_quantity))
                            //{
                                showModal.value=true
                                console.log(searchResult.value)
                                updateScannedItem(searchResult.value)



//                            }
  //                          else scanError.value=`Maximum limit ${searchResult.value.order_qty} reached.`;

                        }
                        else scanError.value=`Item Not found!`;
                    }
                      newItem.value=''
                        }
            ,300)

        );


const form=useForm({
   item_no:'',
   order_qty:0,
   prepacks_total_quantity:0,
   assembled_qty:0,
   assembled_pcs:0,
   qty_base:0,
   item_description:'',
   from_batch:'',
   to_batch:'',
   order_no:'',
   line_no:'',
   customer_spec:''


});


const form2=useForm({
   item_no:'',
   order_qty:0,
   prepacks_total_quantity:0,
   assembled_qty:0,
   assembled_pcs:0,
   qty_base:0,
   item_description:'',
   from_batch:'',
   to_batch:'',
   order_no:'',
   line_no:'',
   customer_spec:'',


});

const ItemInAssembledArray=(item_no)=>{
   const existingItemIndex= assembledArray.value.findIndex(item => item.item_no === item_no)
   return(existingItemIndex!==-1)

}

const submitForm=()=>{
   //push item into assembled array


    //  if ((form2.order_qty-form2.prepacks_total_quantity)!=form.assembled_qty)
    //  {
    //        Swal.fire({
    //                                     title: 'The assembled qty is lower/higher than expected',
    //                                     text: "Are you sure you want to assemble non default qty?",
    //                                     icon: 'warning',
    //                                     showCancelButton: true,
    //                                     confirmButtonColor: '#3085d6',
    //                                     cancelButtonColor: '#d33',
    //                                     confirmButtonText: 'Ok'
    //                                     }).then((result) => {
    //                                         if (!result.isConfirmed) {return }
    //                     });

    //  }


    const existingItemIndex = assembledArray.value.findIndex(item => item.item_no === form.item_no);

        if (existingItemIndex !== -1)
        {
        assembledArray.value.splice(existingItemIndex)
        }
      assembledArray.value.push({ 'item_no':form.item_no,
                                   'assembled_qty':form.assembled_qty,
                                   'assembled_pcs':form.assembled_pcs,
                                   'order_qty':form.order_qty,
                                   'prepacks_total_quantity':0,
                                   'customer_spec':form.customer_spec,

                                //    'prepacks_total_quantity':form.prepacks_total_quantity,
                                   'item_description':form.item_description,
                                   'barcode':form.barcode,
                                    'item_no':form.item_no,
                                    'order_no':form.order_no,
                                    'line_no':form.line_no,
                                    // 'line_no':,
                                    'from_batch':form.from_batch,
                                    'to_batch':form.to_batch

                                });


    showModal.value=false
    newItem.value = '';
    inputField.value.focus();
    form.reset();
    form2.reset();
}

const  cItem=ref({});

const updateScannedItem =(item)=>{
  cItem.value=item;
// console.log(item)
//update form
    form.item_no=item.item_no
    form.barcode=item.barcode
    form.order_qty=item.qty_base
    form.order_pcs=item.order_qty
    form.prepacks_total_quantity=item.prepacks_total_quantity
    form.assembled_qty=item.qty_base
    form.assembled_pcs=item.order_qty
    form.pick_no=props.pick_no
    form.item_description=item.item_description
    form.order_no=item.order_no
    form.line_no=item.line_no
    form.customer_spec=item.customer_spec
    form.batch_no=''


    ///hold the current item statically

    form2.item_no=item.item_no
    form2.barcode=item.barcode
    form2.order_qty=item.qty_base
    form2.order_pcs=item.order_qty
    form2.prepacks_total_quantity=item.prepacks_total_quantity
    form2.assembled_qty=item.order_qty
    form2.assembled_pcs=item.qty_base
    form2.pick_no=props.pick_no
    form2.item_description=item.item_description
    form2.order_no=item.order_no
    form2.line_no=item.line_no
    form2.customer_spec=item.customer_spec



}





const closeAssembly = () => {
    // Swal.fire({
    //     title: 'Are you sure?',
    //     text: "Assembled orders may not be undone!",
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonColor: '#3085d6',
    //     cancelButtonColor: '#d33',
    //     confirmButtonText: 'Close Assembly!',
    //     allowOutsideClick: () => !Swal.isLoading(), // Prevent interaction when loading
    // }).then((result) => {
    //     stopTimer();
    //     if (result.isConfirmed) {
    //         Swal.fire({
    //             title: 'Posting..',
    //             html: '<div class="flex items-center justify-center"><img src="/img/loading.gif" style="width: 100px; height: 100px;"/></div>',
    //             allowOutsideClick: false,
    //             showConfirmButton: false,
    //         });
if (assembledArray.value.length==0) {
    Swal.fire('Error','The assembly is empty','error')
}
else
            Inertia.post(
                route('assembly.store'),
                {
                    'data': assembledArray.value,
                    'part':props.orderLines[0].part,
                    'autosave': false,
                    'assembly_time': formatTime.value,
                },
                {
                    // onSuccess: () => Swal.fire('Success!', 'Assembly Closed Successfully!', 'success'),
                    onError: (error) => Swal.fire('Error', error.message, 'error')
                }
            );
//         }
//     });
};





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

                                               <div class="flex flex-col text-center">
                                                     <h2 class="text-xl font-bold tracking-wide text-red-500"> {{ formatTime }}</h2>
                                                      <!--  <button @click="startTimer" :disabled="isRunning">Start</button>
                                                        <button @click="stopTimer" :disabled="!isRunning">Stop</button>
                                                        <button @click="resetTimer" :disabled="!isRunning && currentTime === 0">Reset</button> -->
<!-- </div>
       <div v-for="assignment in assignments.data" :key="assignment.id">
          <AssignmentCard :assignment="assignment" @click="showContents(assignment.id)" class="shadow-md hover:shadow-lg hover:cursor-pointer hover:shadow-orange-400" />
       </div>

</div> -->

                                                                          <span class="p-2 font-bold tracking-wide text-yellow-500 bg-gray-600 rounded">{{orderLines[0].shp_name}} </span>

                                                       <span class="p-2 font-bold tracking-wide text-yellow-500 bg-gray-600 rounded">{{orderLines[0].sp_search_name}} </span>

                                                  </div>
                                    </div>



                                </template>

                                <template #end>



                                    </template>
                                </Toolbar>


                                <div class="flex flex-row items-center justify-center w-full gap-1 text-center">

                                                    <input type="text" v-model="newItem"  ref="inputField" placeholder="Scan Item" class="m-2 rounded-lg bg-slate-300 text-md">
                                                                <p v-if="scanError" class="p-3 m-3 font-bold text-black bg-red-400 rounded">{{ scanError }}</p>
                                        </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <div class="w-full m-2 text-center">


                                               {{assembledArray.length }} / {{ orderLines.length }}

                                               <ProgressBar :value="Math.round((assembledArray.length)/(orderLines.length)*100)" />

                                            </div>



                                            <div class="grid gap-3 sm:grid-cols-1 md:grid-cols-2 ">

                                                <div class="col-span-1">
                                                    <div  class="w-full p-3 m-2 text-center text-white bg-orange-200"> Ordered</div>
                                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">

                                                        <tbody>
                                                            <tr
                                                              @click="newItem=line.item_no"

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
                                                                    {{ parseFloat(line.order_qty).toFixed(2) }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{ line.customer_spec }}
                                                                </td>

                                                                <!-- <td class="px-3 py-2 text-xs">
                                                                    {{ line.prepacks_total_quantity}}
                                                                </td> -->

                                                                <!-- <td class="px-3 py-2 text-xs text-center text-black bg-yellow-300 rounded-sm">
                                                                    <input
                                                                      class="text-center rounded"
                                                                      v-model="order.ass_qty"
                                                                      :disabled="order.prepacks_total_quantity==order.order_qty"
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
                                                                                                               <tbody>

                                                            <tr v-for="line in assembledArray" :key="line.item_description"
                                                            @click="newItem=line.item_no"

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
                                                                    {{ parseFloat(line.assembled_qty).toFixed(2) }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    {{line.customer_spec }}
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
                                                    <!-- <Pagination :links="orderLines.meta.links" /> -->
                                                     <Button
                                                    class="justify-end"
                                                   label="Close Assembly"
                                                   @click="closeAssembly()"



                                                />

                                                </template>
                                            </Toolbar>
                                        </div>




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
        Customer Spec: {{ form.customer_spec }}
        <div class="flex flex-row justify_between ">
             <span class="px-3 text-center capitalize">Ordered PCS</span>
             <span class="p-3 px-3 text-center text-black capitalize bg-lime-400">{{ cItem.order_qty }}</span>

            <span class="px-3 text-center capitalize">Ordered Weight</span>
            <span class="p-3 px-3 text-center text-white capitalize bg-teal-400">{{ parseFloat(cItem.qty_base).toFixed(2) }}</span>
        </div>
        <div class="flex flex-row justify_between">

            <span class="px-3 text-center capitalize">Prepacked Qty</span>
            <span class="p-3 px-3 text-center text-black capitalize bg-orange-500">{{ form.prepacks_total_quantity?parseFloat(form.prepacks_total_quantity).toFixed(2):0 }}</span>
        </div>
          <div
            class="flex items-center space-x-8"
            >

            <span>PCS </span>

           <InputNumber
                 inputId="integeronly"
             v-model="form.assembled_pcs"
           /></div>



           <div
            class="flex items-center space-x-2"
            >
           <span>Weight</span>
           <InputNumber
            inputId="minmaxfraction" :minFractionDigits="2" :maxFractionDigits="5"
             v-model="form.assembled_qty"
           />
</div>
           <InputText

             v-model="form.from_batch"
             placeholder="From Batch."
           />
           <InputText

             v-model="form.to_batch"
             placeholder="To Batch."
           />

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
