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
import { Link } from '@inertiajs/inertia-vue3';
// import { includes } from 'lodash';
// import {axios } from 'axios';

const inputField=ref(null);
const scanItem=ref(null);

const props= defineProps({
    orderLines:Object,
})

const getRemaining=()=>{
         remainingArray.value = props.orderLines.filter(obj1 =>
                                            !assembledArray.value.some(obj2 => obj2.line_no === obj1.line_no)
                                        );
}

const loadAssembly=()=>{
 if (props.orderLines.length>0)

    {
        for (var i = props.orderLines.length - 1; i >= 0; i--)
        {
          //populate already assembled
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
                        'assembled_qty':result.qty_base,
                        'assembled_pcs':result.order_qty,
                        'order_qty':result.order_qty,
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
    getRemaining();

}

onMounted(() => {
    inputField.value.focus();
    loadAssembly();

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

   const itemInAssembledArray=(line_no)=>{
    const result=assembledArray.value.filter(item=>item.line_no===line_no)
    return(result.length>0)
   }

    watch( newItem,
    debounce( ()=> {
                    startTimer();
                    if (newItem.value.trim()!='' ){
                        scanError.value = '';
                        searchResult.value= searchByBarcodeOrItemNo((newItem.value.toUpperCase()).trim())
                        if (searchResult.value!=0)
                        {
                                showModal.value=true
                                updateScannedItem(searchResult.value)

                        }
                            else scanError.value=`Item Not found!`;
                        }
                        newItem.value=''
                    }
                    ,30
            )

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

const removeLine=(line_no)=>{
    // showModal.value=false
    Swal.fire({
                title: 'Are you sure?',
                text: "This will remove the currently assembled line",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm!'
                }).
                then((result) => {
                    if (result.isConfirmed) {

                            const lineToDelete=assembledArray.value.filter(item=>item.line_no===line_no);
                            assembledArray.value=assembledArray.value.filter(item=>item.line_no!==line_no);

                            const inProps= props.orderLines.filter(item=>item.line_no===line_no)
                            if (inProps.length>0)
                                {
                                     Inertia.post(
                                        route('assembly.remove'),lineToDelete[0],

                                                {
                                                    onError: (error) => Swal.fire('Error', error.message, 'error'),

                                                }
                                          );
                                }
                            getRemaining();
                    }})

}



  const submitForm=()=>{

                        //remove item
                        assembledArray.value= assembledArray.value.filter(item=>item.line_no!==form.line_no);
                        //add item

                        assembledArray.value.push({ 'item_no':form.item_no,
                                                    'assembled_qty':form.assembled_qty,
                                                    'assembled_pcs':form.assembled_pcs,
                                                    'order_qty':form.order_qty,
                                                    'prepacks_total_quantity':0,
                                                    'customer_spec':form.customer_spec,
                                                    'item_description':form.item_description,
                                                    'barcode':form.barcode,
                                                    'item_no':form.item_no,
                                                    'order_no':form.order_no,
                                                    'line_no':form.line_no,
                                                    'from_batch':form.from_batch,
                                                    'to_batch':form.to_batch

                                                });
                    remainingArray.value = props.orderLines.filter(obj1 =>
                                            !assembledArray.value.some(obj2 => obj2.line_no === obj1.line_no)
                                        );



                    showModal.value=false
                    newItem.value = '';
                    inputField.value.focus();
                    form.reset();

                }

const  cItem=ref({});

const updateScannedItem =(item)=>{
    console.log(item)
                            cItem.value=item;
                            form.item_no=item.item_no
                            form.barcode=item.barcode
                            form.order_qty=item.qty_base
                            form.order_pcs=item.order_qty
                            form.prepacks_total_quantity=item.prepacks_total_quantity
                                form.assembled_qty=item.qty_base?parseFloat(item.qty_base):item.assembled_qty
                                form.assembled_pcs=item.order_qty?parseInt(item.order_qty):item.assembled_pcs
                                form.pick_no=props.pick_no
                            form.item_description=item.item_description
                            form.order_no=item.order_no
                            form.line_no=item.line_no
                            form.customer_spec=item.customer_spec
                            form.from_batch=item.from_batch?item.from_batch:''
                            form.to_batch=item.to_batch?item.to_batch:''


                       showModal.value=true
                        }




let allAssembled=true;
let remainingArray=ref([]);




let filteredAssembly=[];
const closeAssembly = () => {
                     if (assembledArray.value.length==0) {
                                        Swal.fire('Error','The assembly is empty','error')
                                    }
                    //check if all lines have been assembled, alert if partial assembly
                    else
                    {
                        for (var i = props.orderLines.length - 1; i >= 0; i--)
                                {
                                    filteredAssembly=assembledArray.value.filter(line=>line.line_no===props.orderLines[i].line_no)
                                    // console.log(filteredAssembly)
                                if (filteredAssembly.length==0)
                                {
                                    allAssembled=false;
                                    break;
                                }
                                }//

                                if (allAssembled)
                                {
                                        Inertia.post(
                                        route('assembly.store'),
                                                {
                                                    'data': assembledArray.value,
                                                    'part':props.orderLines[0].part,
                                                    'autosave': false,
                                                    'assembly_time': formatTime.value,
                                                },
                                                {
                                                    onError: (error) => Swal.fire('Error', error.message, 'error')
                                                }
                                         );


                                }

                            else
                            {
                              Swal.fire({
                                        title: 'Partial Assembly?',
                                        text: "One or more lines have not been assembled. Confirm Partial Assembly?",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Confirm!'
                                        }).
                                        then((result) => {
                                            if (result.isConfirmed) {

                                               Inertia.post(
                                                    route('assembly.store'),
                                                    {
                                                        'data': assembledArray.value,
                                                        'part':props.orderLines[0].part,
                                                        'autosave': false,
                                                        'assembly_time': formatTime.value,
                                                    },
                                                    {
                                                        onError: (error) => Swal.fire('Error', error.message, 'error')
                                                    }
                                                    );

                                            }})

                             }
                        }
                };
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

                                                                                    v-for="line in remainingArray" :key="line.item_description"

                                                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-400 hover:text-white ">
                                                                                    <div v-if="!itemInAssembledArray(line.line_no)" class="flex justify-between">
                                                                                        <td class="px-1 text-xs">
                                                                                            {{ line.item_no }}
                                                                                        </td>
                                                                                        <td class="px-1 text-xs">
                                                                                            {{ line.item_description }}
                                                                                        </td>

                                                                                        <td class="px-1 text-xs">
                                                                                            {{ parseFloat(line.order_qty).toFixed(2) }}
                                                                                        </td>
                                                                                        <td class="px-1 text-xs">
                                                                                            {{ line.customer_spec }}
                                                                                        </td>

                                                                                    </div>
                                                                                </tr>

                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="col-span-1"  >
                                                                        <div  class="w-full p-3 m-2 text-center text-white bg-slate-400"> Assembled</div>
                                                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 ">
                                                                            <tbody>
                                                                               <tr>

                                                                                <th class="text-xs">Description</th>
                                                                                <!-- <th class="text-xs">BarCode</th> -->
                                                                                <th class="text-xs">PCS</th>
                                                                                <th class="text-xs">WT</th>
                                                                                <th class="text-xs">SPEC</th>
                                                                                <th class="text-xs">Batch</th>
                                                                               </tr>
                                                                                <tr v-for="line in assembledArray" :key="line.line_no"



                                                                                class="items-center bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-400 hover:text-white ">




                                                                                    <td class="px-1 text-xs">
                                                                                        {{ line.item_description }}
                                                                                    </td>

                                                                                    <td class="px-1 text-xs">
                                                                                        {{ parseFloat(line.assembled_pcs).toFixed(2) }}
                                                                                    </td>
                                                                                    <td class="px-1 text-xs">
                                                                                        {{ parseFloat(line.assembled_qty).toFixed(2) }}
                                                                                    </td>
                                                                                    <td class="px-1 text-xs">
                                                                                        {{line.customer_spec }}
                                                                                    </td>
                                                                                    <td>
                                                                                        {{line.from_batch}}-{{ line.to_batch }}
                                                                                    </td>
                                                                                    <td >
                                                                                        <Button

                                                                                         severity="info"
                                                                                         icon="pi pi-pencil"
                                                                                         text
                                                                                         rounded
                                                                                         style="height: 50%;"

                                                                                         @click="updateScannedItem(line)"

                                                                                        />
                                                                                    </td>
                                                                                    <td>
                                                                                        <Button

                                                                                         severity="danger"
                                                                                         icon="pi pi-times"
                                                                                            text
                                                                                            rounded
                                                                                            style="height: 50%;"

                                                                                         @click="removeLine(line.line_no)"

                                                                                        />
                                                                                    </td>



                                                                            </tr>


                                                                        </tbody>
                                                                    </table>


                                                                </div>
                                                            </div>
                                                            <Toolbar>
                                                                <template #center>
                                                                    <!-- <Pagination :links="orderLines.meta.links" /> -->
                                                                    <div class="flex justify-center gap-4 ">


                                                                <Link
                                                                        :href="route('assembly.index')"

                                                                        ><Button
                                                                        icon="pi pi-backward"
                                                                        label="Back"
                                                                    /></Link>

                                                                    <Button
                                                                    class="justify-end"
                                                                    label="Save"
                                                                    severity="success"
                                                                    @click="closeAssembly()"
                                                                  />
                                                                    </div>
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
                                            <span class="p-3 font-bold text-center capitalize bg-yellow-400 rounded-lg">{{ form.item_description }}</span>
                                            <span class="w-full p-2 font-bold text-black bg-orange-400 rounded-lg">Customer Spec: {{ form.customer_spec }}</span>
                                            <div class="grid grid-cols-2 ">
                                                <div class="">
                                                    <span class="px-3 text-center capitalize">Ordered PCS:</span>
                                                    <span class="py-3 font-bold">{{ cItem.order_qty }}</span>
                                                </div>

                                               <div>
                                                  <span class="px-3 text-center capitalize">Ordered Weight:</span>
                                                  <span class="py-3 font-bold">{{ parseFloat(cItem.qty_base).toFixed(2) }}</span>
                                               </div>

                                            </div>
                                            <div class="">

                                                <span class="px-3 text-center capitalize">Prepacked Qty:</span>
                                                <span class="py-3 font-bold">{{ form.prepacks_total_quantity?parseFloat(form.prepacks_total_quantity).toFixed(2):0 }}</span>
                                            </div>

                                            <div
                                            class="grid grid-cols-2 gap-x-2 gap-y-2"
                                            >
                                          <div>

                                            <span class="font-bold">PCS </span>

                                            <InputNumber
                                            inputId="integeronly"
                                            v-model="form.assembled_pcs"
                                            /></div>
                                                    <div
                                                    class="flex items-center space-x-2"
                                                    >
                                                    <span class="font-bold">Weight</span>
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
                                          </div>










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
