<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import {watch, ref,onMounted, nextTick,reactive,computed,onUnmounted} from 'vue';
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce'
import ProgressBar from 'primevue/progressbar';
import { useSearchArray } from '@/Composables/useSearchArray';
import jsPDF from 'jspdf';
  import QRCode from 'qrcode-generator';
  import axios from 'axios';

const openModal = () => {
  if (pdfDataUrl.value) {




    Swal.fire({
      title: 'Packing Label',
      html: `
        <div id="pdf-modal">
          <iframe src="${pdfDataUrl.value}" width="100%" height="400px"></iframe>
        </div>`,
      showConfirmButton: false,
    });
  } else {
    Swal.fire({
      title: 'PDF not generated',
      text: 'Please generate the PDF first.',
      icon: 'error',
    });
  }
};
const inputField=ref(null);
const scanItem=ref(null);
const pdfDataUrl = ref('');

let checker_id=ref('');

const props= defineProps({
                            orderLines:Object,
                            checkers_list:Object,
                            user:Object,
                        });

const assembledArray=ref([]);

const generatePDF = (from=1,to=1) =>
{


    const doc = new jsPDF({
                            orientation: "portrait",
                            unit: "cm",
                            format: [5, 7.5]
                            });
    // doc.setMargins(0.5, 0.5, 0.5, 0.5);
   //save the carton numbers for swiping later
/**
 *
 *  $table->id();
            $table->string('vessel_type');
            $table->string('vessel_no');
            $table->string('order_no');
            $table->string('part');
            $table->foreignIdFor(User::class);
            $table->unsignedBigInteger('packed_by')->references('id')->on('users')->nullable();
            $table->unsignedBigInteger('loaded_by')->references('id')->on('users')->nullable();
            $table->dateTime('loading_time')->nullable();
            $table->timestamps();
 */


    const center=(text)=>{
        const textWidth = doc.getStringUnitWidth(text) * doc.internal.getFontSize() / doc.internal.scaleFactor;
    return (doc.internal.pageSize.width - textWidth) / 2;
    }


    let v=1;
    const allPagesContent = [];
    let fontSizeFactor=1;
   let globalVesselNo=ref('');

    for (let pageNum = from; pageNum <= to; pageNum++)
    {
            if (pageNum > from)
            {
                v++;
                doc.addPage();
            }
        //    alert(form.vessel);
        // console.log(form.vessel);

            axios.post(route('vessels.store'),{
                'order_no':props.orderLines.data[0].order.order_no,
                'part':props.orderLines.data[0].part,
                'vessel_type':form.vessel,
                'vessel_no':pageNum,
                // 'user_id':props.user.data.id,
            })
            .then((response)=>{
                console.log(response.data);
                  globalVesselNo.value=response.data.id;




            })
            .catch((response)=>{
                Swal.fire('Error', response.data.message, 'error')
                // console.log(response)
        });





            const qrCodeText=route('loadVessel')+'?order_no='+encodeURIComponent(props.orderLines.data[0].order.order_no)+'?part='+props.orderLines.data[0].part+'?vessel_no='+pageNum;
            // console.log(qrCodeText);
            const lineHeight = 0.5;
            const qrCode = new QRCode(0, 'H');
            qrCode.addData(qrCodeText);
            qrCode.make();
            const qrCodeDataUrl = qrCode.createDataURL(4);
            //12 chars is 10

            if (props.orderLines.data[0].order.shp_name.length<=12)


             doc.setFontSize(10);
            else
             doc.setFontSize(6);

             doc.setFont("helvetica", "bold");
            doc.text(props.orderLines.data[0].order.shp_name, center(props.orderLines.data[0].order.shp_name), 1);

            doc.text(props.orderLines.data[0].order.order_no, center(props.orderLines.data[0].order.order_no), 1+lineHeight);
            doc.text('Part-'+props.orderLines.data[0].part, center(props.orderLines.data[0].part), 1+2*lineHeight);

            if (props.orderLines.data[0].order.sp_search_name.length<=12)


             doc.setFontSize(10);
            else
            doc.setFontSize(8);
            doc.setFont("helvetica", "normal");
            doc.text(props.orderLines.data[0].order.sp_search_name, center(props.orderLines.data[0].order.sp_search_name),1+ 3*lineHeight);
            doc.text(form.vessel+'-'+pageNum, center(form.vessel+'-'+pageNum),1+ 4*lineHeight);
            doc.text('Packer : '+props.user.data.user_name,center('Packer : '+props.user.data.user_name),1+ 5*lineHeight);
            doc.text('Serial No. : '+ globalVesselNo.value,center('Serial No. : '+ globalVesselNo.value),1+ 6*lineHeight);
            doc.addImage(qrCodeDataUrl, 'JPEG', 1.5, 5, 2, 2);
            // const pageContent = ;
  }

    allPagesContent.push(doc.output('datauristring'));
    pdfDataUrl.value = allPagesContent;
    //doc.save('label.pdf')
    openModal();

};


 const qrCodeImage=(text)=> {
    // Create and return a QR code image using your preferred QR code library
    // You can use a library like qrcode-generator or qrcode-svg
    // Here's a simplified example using an SVG QR code:
    const qrCode = new QRCode(text, 4);
    return qrCode.getBase64();
  };

onMounted(() => {
    inputField.value.focus();


    setInterval(() => {
        if (!assembledArray.value.length==0 && isRunning.value==true)
           Inertia.post(route('packing.store'),{'data':assembledArray.value,
                                                    'autosave':true,
                                                    'checker_id':checker_id,
                                                    'packing_time':formatTime.value,

                                                },{preserveScroll:true,preserveState:true}
                                                )

                     }, 60000);
    // console.log(form.vessel.value)
//populate assembled array

if (props.orderLines.data.length>0)

{
   for (var i = props.orderLines.data.length - 1; i >= 0; i--)
   {




    for (var j = props.orderLines.data[i].packing.length - 1; j >= 0; j--)
    {
     const result = searchByMultipleKeyValues([
                                                  ['line_no', props.orderLines.data[i].packing[j].line_no],
                                                  ['order_no', props.orderLines.data[i].packing[j].order_no]

                                                ]);



      if (result.value!=0)
      {
        assembledArray.value.push({
                                   'item_no':result.item_no,
                                   'item_description':result.item_description,
                                    'order_no':result.order_no,
                                    'line_no':result.line_no,
                                    'packed_qty':props.orderLines.data[i].packing[j].packed_qty,
                                    'packed_pcs':props.orderLines.data[i].packing[j].packed_pcs,
                                    'vessel':props.orderLines.data[i].packing[j].vessel,
                                    'from_vessel':props.orderLines.data[i].packing[j].from_vessel,
                                    'to_vessel':props.orderLines.data[i].packing[j].to_vessel,
                                    'from_batch':props.orderLines.data[i].packing[j].from_batch,
                                    'to_batch':props.orderLines.data[i].packing[j].to_batch,

                            });

      };


    };

};

};

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
const  vessels=ref([
                        // { label: '', value: '',selected:true },
                        { label: 'Crate', value: 'Crate' },
                        { label: 'Carton', value: 'Carton' },

                    ]);




const extractedData = ref(Object.entries(props.orderLines.data).map(([key, value]) => {

    return {

            'order_qty': value.order_qty ,// Use the value with ref/ Extract 'age' key as value with ref
            'prepacks_total_quantity': value.prepacks_total_quantity ,// Use the value with ref/ Extract 'age' key as value with ref
            'barcode':value.barcode,
            'item_no':value.item_no,
            'item_description':value.item_description,
            'order_no':value.order_no,
            'line_no':value.line_no,
            'packed_qty':value.order_qty,
            'packed_pcs':value.order_qty,
            'carton_no':value.carton_no,
            'vessel':value.vessel,
            'from_vessel':value.from_vessel,
            'to_vessel':value.to_vessel,
            'from_batch':value.from_batch,
            'to_batch':value.to_batch
          };
}));

const searchKey = ref('');
const searchValue = ref('');
const {searchByBarcodeOrItemNo,searchByMultipleKeyValues } = useSearchArray(extractedData)
const searchResult = ref(0);

/*

   if an item has already been packed, populate the assembled array with the packed items


*/







watch( newItem,
 debounce(
            function () {
                // alert(newItem.value);

                startTimer();
                if (newItem.value.trim()!='' ){
                            scanError.value = '';

                       searchResult.value= searchByBarcodeOrItemNo((newItem.value.toUpperCase()).trim())
                       if (searchResult.value!=0)
                        {

                            if (parseFloat(searchResult.value.packed_qty)>parseFloat(searchResult.value.prepacks_total_quantity))
                            {
                                showModal.value=true
                                updateScannedItem(searchResult.value)
                             }
                            else scanError.value=`Maximum limit ${searchResult.value.order_qty} reached.`;

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
   item_description:'',
   batch_no:'',
   order_no:'',
   line_no:'',
   packed_qty:0,
   packed_pcs:0,
   // carton_no:1,
   vessel:'',
   from_vessel:0,
   to_vessel:0,
    from_batch:'',
   to_batch:'',
   empty:false,

});





const form2=useForm({
   item_no:'',
   order_qty:0,
   prepacks_total_quantity:0,
   assembled_qty:0,
   item_description:'',
   batch_no:'',
   order_no:'',
   line_no:'',
   packed_qty:0,
   packed_pcs:0,
   // carton_no:1,
   vessel:'',
   from_vessel:1,
   to_vessel:1,
   from_batch:'',
   to_batch:'',
   empty:false,

});


watch(form.empty, ()=>{alert('here')})
watch(form2.empty, ()=>{if(form2.empty.value){form2.assembled_qty.value=0; form2.packed_qty.value=0;}})

const ItemInAssembledArray=(item_no)=>{
   const existingItemIndex= assembledArray.value.findIndex(item => item.item_no === item_no)
   return(existingItemIndex!==-1)

}

const submitForm=()=>{
   //push item into assembled array



     if ((form2.packed_qty)!=form.order_qty)
     {
           Swal.fire({
                                        title: 'The packed qty is lower/higher than expected',
                                        text: "Are you sure you want to pack  non default qty?",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (!result.isConfirmed) { return}
                        });

     }


    const existingItemIndex = assembledArray.value.findIndex(item => item.item_no === form.item_no);




    if (existingItemIndex !== -1)
    {
      // If the key already exists, update the value
      //   alert('here')

      assembledArray.value[existingItemIndex].packed_qty = form.packed_qty;
      assembledArray.value[existingItemIndex].packed_pcs = form.packed_pcs;
      assembledArray.value[existingItemIndex].from_batch = form.from_batch;
      assembledArray.value[existingItemIndex].to_batch = form.to_batch;
      assembledArray.value[existingItemIndex].from_vessel = form.from_vessel;
      assembledArray.value[existingItemIndex].to_vessel = form.to_vessel;
      assembledArray.value[existingItemIndex].vessel = form.vessel;

    //   assembledArray.value[existingItemIndex].assembled_qty = form.packed_qty;

    } else
    {
      // If the key doesn't exist, push a new key-value pair

      assembledArray.value.push({   'order_no':form.order_no,
                                    'item_no':form.item_no,
                                    'item_description':form.item_description,
                                    'line_no':form.line_no,
                                    'packed_qty':form.packed_qty,
                                    'packed_pcs':form.packed_pcs,
                                    'vessel':form.vessel,
                                    'from_vessel':form.from_vessel,
                                    'to_vessel':form.to_vessel,
                                    'from_batch':form.from_batch,
                                    'to_batch':form.to_batch,
                                });
    }

    showModal.value=false
    newItem.value = '';
    inputField.value.focus();
     form.reset();
        form2.reset();
}



const updateScannedItem =(item)=>{


 //update batch no from assembly






//update form
    form.item_no=item.item_no
    form.barcode=item.barcode
    form.order_qty=item.order_qty
    form.prepacks_total_quantity=item.prepacks_total_quantity
    form.assembled_qty=item.order_qty-item.prepacks_total_quantity
    form.pick_no=props.pick_no
    form.item_description=item.item_description
    form.order_no=item.order_no
    form.line_no=item.line_no
    form.to_batch=item.to_batch
    form.from_batch=item.from_batch
    form.packed_qty=item.packed_qty
    form.packed_qty=item.packed_qty
    form.carton_no=item.carton_no
    form.vessel=item.vessel
    form.to_vessel=item.to_vessel
    form.from_vessel=item.from_vessel


    ///hold the current item statically

    form2.item_no=item.item_no
    form2.barcode=item.barcode
    form2.order_qty=item.order_qty
    form2.prepacks_total_quantity=item.prepacks_total_quantity
    form2.assembled_qty=item.order_qty-item.prepacks_total_quantity
    form2.pick_no=props.pick_no
    form2.item_description=item.item_description
    form2.order_no=item.order_no
    form2.line_no=item.line_no
    form2.to_batch=item.to_batch
    form2.from_batch=item.from_batch
    form2.packed_qty=item.packed_qty
    form2.packed_pcs=item.packed_pcs
    form2.carton_no=item.carton_no
    form2.vessel=item.vessel
    form2.to_vessel=item.to_vessel
    form2.from_vessel=item.from_vessel



}





const closeAssembly=()=>{

     //if the assembled quantity is not equal to the ordered quantity

     Swal.fire({
                    title: 'Are you sure?',
                    text: "Packed orders may not be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Pack Order!'
                    }).then((result) => {
                        if (result.isConfirmed)
                        {

                          stopTimer();
                          // console.log(formatTime);
                            Inertia.post(route('packing.store'),{'data':assembledArray.value,
                                                                 'packing_time':formatTime.value,
                                                                 'checker_id':checker_id,
                                                                 'autosave':false,
                                                                }
                                         );


                        }
    })
}

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
    <Head title="Packing"/>

    <AuthenticatedLayout  @add="showModal=true">
        <!-- <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Parts</h2>
        </template> -->

        <div class="py-3">

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">



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

                                                        <span class="p-2 font-bold tracking-wide text-yellow-500 bg-gray-600 rounded">{{orderLines.data[0].order.shp_name}} </span>

                                                       <span class="p-2 font-bold tracking-wide text-yellow-500 bg-gray-600 rounded">{{orderLines.data[0].order.sp_search_name}} </span>
                                                  </div>
                                    </div>



                                </template>

                                <template #end>
                                    <!-- {{ dc }} -->




                                    </template>
                                </Toolbar>

                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">


                                </div>



                                        <div class="flex flex-row items-center justify-center w-full gap-1 text-center">

                                                    <input type="text" v-model="newItem"  ref="inputField" placeholder="Scan Item" class="m-2 rounded-lg bg-slate-300 text-md">
                                                                <p v-if="scanError" class="p-3 m-3 font-bold text-black bg-red-400 rounded">{{ scanError }}</p>
                                        </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <div class="w-full m-2 text-center">


                                               {{assembledArray.length }} / {{ orderLines.data.length }}

                                               <ProgressBar :value="Math.round((assembledArray.length)/(orderLines.data.length)*100)" />

                                            </div>



                                            <div class="grid gap-3 sm:grid-cols-1 md:grid-cols-2 ">

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
                                                               @click="newItem=line.item_no"
                                                                v-for="line in orderLines.data" :key="line.item_description"

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
                                                                    {{ line.order_qty }}
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
                                             <div  class="w-full p-3 m-2 text-center text-white bg-slate-400"> Packed</div>
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
                                                                    #:{{ line.from_batch}}-
                                                                    {{ line.to_batch}}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs">
                                                                    Qty:{{ line.packed_qty }}
                                                                    PCS:{{ line.packed_pcs }}
                                                                </td>
                                                                 <td class="px-3 py-2 text-xs">
                                                                    {{line.vessel}}:{{ line.from_vessel }}-{{line.to_vessel}}
                                                                </td>



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
                                                 <!-- <ul>
                                                    <li class="p-3" v-for="item in assembledArray">{{ item }}</li>
                                                 </ul> -->

                                                </div>
                                            </div>
                                            <Toolbar>

                                                <template #center>

                                                     <!-- <generate-pdf :data="assembledArray"></generate-pdf> -->
                                                     <div class="flex flex-col items-center space-y-2 text-center">


                                                       <div class="space-x-3">
                                        Checker:
                                        <Dropdown
                                            :options="checkers_list"
                                            optionValue="id"
                                            optionLabel="name"
                                             v-model="checker_id"
                                            filter
                                        />

                                    </div>

                                                  <div>
                                                    <Button
                                                    class="justify-end"
                                                    severity="warning"
                                                   label="End Packing"
                                                   :disabled="checker_id==''"
                                                   @click="closeAssembly()"

                                              />

                                                  </div></div>
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

    <div class="grid place-items-center">

    <div class="w-full p-4 font-bold text-center text-white bg-slate-600"> Packing</div>


        <form @submit.prevent="submitForm()" class="flex flex-col gap-2 text-center">

        <span class="text-xl font-bold capitalize text-cyan-800">{{ form.item_description }}</span>
            <div class="flex items-center space-x-2">
            <span class="px-3 text-center capitalize">Ordered Qty</span>

            <span class="px-3 text-center capitalize">{{ form.order_qty }}</span>
            <span class="px-3 text-center capitalize">Prepacked Qty</span>
            <span class="px-3 text-center capitalize">{{ form.prepacks_total_quantity }}</span>
            </div>

             <div class="flex items-center space-x-2 text-center ">
             <span class="px-3 text-center capitalize">Pieces</span>
             <InputText
                    placeholder="Pieces"
                    ref="scanItem"
                    style="width: 5em "
                    class="mx-5"
                    v-model="form.packed_pcs"

                />
                </div>
                <div class="flex items-center space-x-2">
                <span class="px-3 text-center capitalize">Weight</span>
              <InputText
                    placeholder="Weight"
                    style="width: 5em "
                    class="mx-5"
                    ref="scanItem"
                    v-model="form.packed_qty"
                    :placeholder="form.packed_qty"
                />

            </div>





          <div class="flex space-x-2">
            <Dropdown
            style="width: 10em "
               v-model="form.vessel"
               :options=vessels
               optionLabel='label'
               optionValue='value'
               placeholder='Vessel'
               class="mx-2"


             />


            <InputText
             size="2"
            placeholder="From "

             class="p-1 mx-2 rounded"
             v-model="form.from_vessel"


           />
          <InputText
           size="2"
              placeholder="To"
             class="p-1 mx-2 rounded"
             v-model="form.to_vessel"

           />
           </div>
      <div class="flex items-center space-x-2">
      <span class="px-3 text-center capitalize">Batch</span>
                <InputText
                      size="5"
                      v-model="form.from_batch"
                      placeholder="From Batch"
                        class="mx-2"
                 />

                <InputText
                        size="5"
                         placeholder="To Batch"
                        v-model="form.to_batch"
                         class="mx-2"
                />
           Empty? <input type="checkbox" v-model="form.empty"  />

         </div>

              <Button @click="generatePDF(form.from_vessel,form.to_vessel)"
                v-show="((parseInt(form.from_vessel)>0)&&(form.from_batch!='')&&(form.vessel!=null))||form.empty"
                label="Close Carton"
                severity="warning"
              />

            <Button  label="Pack"
             v-show="((parseInt(form.from_vessel)>0)&&(form.from_batch!='')&&(form.vessel!=null))||form.empty"
            icon="pi pi-send" class="w-md" severity="success"  type="submit" :disabled="form.processing" />
            <Button label="Cancel" icon="pi pi-cancel"  severity="danger"  @click="showModal=false" class="w-md"/>


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
