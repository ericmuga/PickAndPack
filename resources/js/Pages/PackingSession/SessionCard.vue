<script setup>
import Toolbar from 'primevue/toolbar';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import {ref,onMounted} from 'vue';
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'
import jsPDF from 'jspdf';
import QRCode from 'qrcode-generator';
import axios from 'axios';
import { watch,computed,toRefs } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import Button from 'primevue/button';

const closePacking=()=>{

    //if the assembled quantity is not equal to the ordered quantity

    Swal.fire({
        title: 'Are you sure?',
        text: "Packed orders may not be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'End Session!'
    }).then((result) => {
        if (result.isConfirmed)
        {
            Inertia.post(route('packingSession.close'),{'id':props.session.data.id},{
                onSuccess:()=>Swal.fire('Success!','Session Ended Successfully!','success'),
                onError:(error)=>Swal.fire('Error',error.message,'error')
            }

            )
        }
    })
}


const drop=(dropRoute)=>Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
})
.then( (result) => {if (result.isConfirmed) {Inertia.delete(dropRoute)}
window.location.reload()
})



const calculatedSum = ref([]);
let printedArray=ref();
onMounted(() => {
    calculateSum();
    printedArray.value=props.printedArray
});


const calculateSum = () => {
    const sumsMap = new Map();

    lines.value.forEach((item, index) => {
        const key = `${item.packing_vessel.code}-${item.from_vessel}-${item.to_vessel}`;

        if (!sumsMap.has(key)) {
            sumsMap.set(key, {
                weight: 0,
                tare_weight: 0,
                qty: 0,
            });
        }

        const sum = sumsMap.get(key);
        sum.weight += parseFloat(item.weight);
        sum.qty += parseFloat(item.qty);

        // Sum tare_weight only when the key changes
        if (index === 0 || key !== `${lines.value[index - 1].packing_vessel.code}-${lines.value[index - 1].from_vessel}-${lines.value[index - 1].to_vessel}`) {
            sum.tare_weight += parseFloat(item.packing_vessel.tare_weight);
        }
    });

    const result = Array.from(sumsMap, ([key, sum]) => ({
        packing_vessel_id: key.split('-')[0],
        from_vessel: key.split('-')[1],
        to_vessel: key.split('-')[2],
        weight: sum.weight,
        tare_weight: sum.tare_weight,
        qty: sum.qty,
    }));

    calculatedSum.value = result;
};


const calculateTotals = computed(() => {
    let totalWeight = 0;
    let totalTareWeight = 0;
    let totalCount = 0;

    calculatedSum.value.forEach(entry => {
        const fromVessel = parseInt(entry.from_vessel);
        const toVessel = parseInt(entry.to_vessel);
        const vesselCount = toVessel - fromVessel + 1;

        totalWeight += parseFloat(entry.weight);
        totalTareWeight += parseFloat(entry.tare_weight);
        totalCount += vesselCount;
    });

    return [totalWeight, totalTareWeight, totalCount];
});

let calc= ref('')


const evaluateExpression = () => {
    try {
        // Use eval to evaluate the mathematical expression
        form.weight = eval(calc.value);

        // Optionally, you can clear the input after evaluation
        calc.value = '';
    } catch (error) {
        console.error('Error evaluating expression:', error);
        // Handle invalid expressions or errors
    }
};


const dataURIsToBlobs = (dataURIs) => {
    const blobs = [];

    try {
        if (!Array.isArray(dataURIs)) {
            throw new Error('Invalid data URIs array');
        }

        dataURIs.forEach((dataURI) => {
            if (!dataURI || typeof dataURI !== 'string' || !dataURI.startsWith('data:')) {
                throw new Error('Invalid data URI');
            }

            const byteString = atob(dataURI.split(',')[1]);
            const arrayBuffer = new ArrayBuffer(byteString.length);
            const uint8Array = new Uint8Array(arrayBuffer);

            for (let i = 0; i < byteString.length; i++) {
                uint8Array[i] = byteString.charCodeAt(i);
            }

            blobs.push(new Blob([arrayBuffer], { type: 'application/pdf' }));
        });

        return blobs;
    } catch (error) {
        console.error('Error converting data URIs to Blobs:', error);
        return [];
    }
};

const validateQty=(itemNo)=>{

    // Filter the array based on matching from_vessel and to_vessel
    //  console.log(itemNo);

    let max=getItemOrderQty(form.item_no)-getItemPackedQty(form.item_no)
    if (form.qty>max){
        Swal.fire('Caution!','The packed quantity exceeds the ordered quantity!','warning')
    }

    const filteredData = props.OrderLines.data.filter(item => item.item_no ===itemNo);
    console.log(filteredData[0])
    // if (filteredData[0].qty_base!=0)
    form.weight=form.qty*(filteredData[0].qty_base/filteredData[0].order_qty)


}

const validateWeight=(itemNo)=>{

    // Filter the array based on matching from_vessel and to_vessel
    //  console.log(itemNo);

    let max=getItemQtyPer(itemNo)*1.15*form.qty
    let min=getItemQtyPer(itemNo)*0.85*form.qty

    if (form.weight>max||form.weight<min){
        Swal.fire('Caution!','The packed weight is not within the expected range!','warning')
    }



}


const sumPackedQtyByVessel=(fromVessel, toVessel)=> {
    // Filter the array based on matching from_vessel and to_vessel
    const filteredData = assembledArray.value.filter(item => item.from_vessel === fromVessel && item.to_vessel === toVessel);

    // Calculate the sum of packed_qty for the filtered items
    const sumPackedQty = filteredData.reduce((sum, item) => sum + parseFloat(item.packed_qty), 0);

    return sumPackedQty;
}


const sumPackedQty=(itemNo)=> {
    // Filter the array based on matching from_vessel and to_vessel
    const filteredData = props.lines.filter(item => item.item_no === itemNo);

    // Calculate the sum of packed_qty for the filtered items
    const sumQty = filteredData.reduce((sum, item) => sum + parseFloat(item.qty), 0);

    return sumQty;
}


const openModal = () => {

    if (pdfDataUrl.value)
    {




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

const assembledArray=ref([]);

const selectedFile=ref();



function checkMatchingRanges(secondObject) {
    // Check if firstArray is defined and not empty
    let firstArray=printedArray.value;
    // console.log(Array.isArray(firstArray))
    if (Array.isArray(firstArray) && firstArray.length > 0) {
        return firstArray.some(obj => obj.range_start === secondObject.from_vessel && obj.range_end === secondObject.to_vessel);
    }

    // If firstArray is not defined or empty, return false
    return false;
}


const generatePDF = (from=1,to=1,vessel='',weight) =>
{

    // console.log()


    const doc = new jsPDF({
        orientation: "portrait",
        unit: "cm",
        format: [5, 7.5]
    });

    const center=(text)=>{
        const textWidth = doc.getStringUnitWidth(text) * doc.internal.getFontSize() / doc.internal.scaleFactor;
        return (doc.internal.pageSize.width - textWidth) / 2;
    }

    var maxWidth = 100;
    const  getTextWidth=(text)=> {
        var textWidth = doc.getStringUnitWidth(text) * doc.internal.getFontSize();
        return textWidth;
    }

    const  wrapText=(text)=> {
        var words = text.split(' ');
        var lines = [];
        var currentLine = '';

        for (var i = 0; i < words.length; i++) {
            var word = words[i];
            var width = getTextWidth(currentLine + ' ' + word);

            if (width < maxWidth) {
                currentLine += (currentLine === '' ? '' : ' ') + word;
            } else {
                lines.push(currentLine);
                currentLine = word;
            }
        }
        // Add the last line
        lines.push(currentLine);
        return lines;
    }



    let lines='';

    let v=1;
    const allPagesContent = [];
    let fontSizeFactor=1;
    let globalVesselNo=ref('');
    let lineHeight=0.5;



    for (let pageNum = from; pageNum <= to; pageNum++)
    {
        if (pageNum > from)
        {
            v++;
            doc.addPage();
        }


        // alert(form.vessel)
        axios.post(route('vessels.store'),
        {
            'order_no':props.session.data.order_no,
            'part':props.session.data.part,
            'vessel_type':vessel,
            'vessel_no':pageNum,
            'range_start':from,
            'range_end':to,

        })
        .then(response=>{
            printedArray.value=response.data.data;

        })

        .catch(error=>{ Swal.fire('Error',error.response.data.message,'error')
        //    console.log(error)
    })





    const qrCodeText=props.lines[0].order_no+'_'+props.OrderLines.data[0].part+'_'+pageNum;

    const qrCode = new QRCode(0, 'H');
    qrCode.addData(qrCodeText);
    qrCode.make();
    const qrCodeDataUrl = qrCode.createDataURL(4);
    //12 chars is 10

    if (props.session.data.order.shp_name.length<=12)


    doc.setFontSize(12);
    else


    doc.setFont("helvetica", "bold");
    let g=0;

    //  if (props.orderLines.data[0].order.shp_name.length>)
    // {
        lines = wrapText(props.session.data.order.shp_name);

        for (var i = 0; i < lines.length; i++) {
            if (i==0)
            doc.text(lines[i] ,center(lines[i]), 1)
            else
            doc.text(lines[i] ,center(lines[i]), 1+(i*lineHeight))

            g++;

            // doc.text(20, 20 + i * 10, lines[i]);
        }
        // }
        //else
        //   doc.text(props.orderLines.data[0].order.shp_name, center(props.orderLines.data[0].order.shp_name), 1);

        doc.setFontSize(8);
        doc.text(props.session.data.order.order_no+'-'+props.session.data.part, center(props.session.data.order.order_no+'-'+props.session.data.part), 1+(g)*lineHeight);
        doc.text('Gross WT/Ves.:'+weight+'Kgs.', center('Gross WT/Ves.:'+weight+'Kgs.'), 1+(g+1)*lineHeight);

        if (props.session.data.order.sp_search_name.length<=12)
        doc.setFontSize(12);
        else
        doc.setFontSize(10);

        doc.setFont("helvetica", "normal");
        doc.text(vessel+'-'+pageNum ,center(vessel+'-'+pageNum),1+ (g+2)*lineHeight);
        doc.addImage(qrCodeDataUrl, 'JPEG', 1.5, (g+5.25)*lineHeight, 2, 2);
        doc.setFontSize(12);
        doc.setFont("helvetica", "bold");


        if (props.session.data.order.sp_search_name.length>18)
        {
            let f=0;
            let lines2=[];
            lines2 = wrapText(props.session.data.order.sp_search_name);

            for (var i = 0; i < lines2.length; i++)
            {
                doc.text(lines2[i] ,center(lines2[i]), 1+(g+8+f)*lineHeight)
                f++;
            }


        }
        else

        doc.text(props.session.data.order.sp_search_name, center(props.session.data.order.sp_search_name),1+ (g+9)*lineHeight);
        // const pageContent = ;
    }

    allPagesContent.push(doc.output('datauristring'));
    pdfDataUrl.value = allPagesContent;


    const pdfDataUri =pdfDataUrl.value;

    // Create a Blob from the data URI
    //   const blob = dataURItoBlob(pdfDataUri);
    const blobs = dataURIsToBlobs(allPagesContent);

    // Create a File from the Blob
    blobs.forEach((blob, index) => {
        const formData = new FormData();
        formData.append('pdfFile', blob);
        formData.append('pageNumber', index + 1); // You may want to include the page number or other relevant info
        formData.append('order',convertToValidFilename(props.session.data.order.order_no+props.session.data.part+'-'+props.session.data.order.shp_name));

        axios.post(route('vessels.upload'), formData)
        .then((response) => {
            //   console.log(`Page ${index + 1} uploaded successfully`, response.data);
        })
        .catch((error) => {
            console.error(`Error uploading page ${index + 1}:`, error);
        });
    });






    //doc.save('label.pdf')
    openModal();

};

function convertToValidFilename(inputString) {
    // Replace spaces and special characters with underscores
    const validFilename = inputString.replace(/[^\w.]/g, '_');

    // Optionally, you can remove consecutive underscores
    return validFilename.replace(/_+/g, '_');
}

const qrCodeImage=(text)=> {
    // Create and return a QR code image using your preferred QR code library
    // You can use a library like qrcode-generator or qrcode-svg
    // Here's a simplified example using an SVG QR code:
    const qrCode = new QRCode(text, 4);
    return qrCode.getBase64();
};




const props=defineProps({
    OrderLines:Object,
    session:Object,
    lastVessel:String,
    packingVessels:Object,
    lines:Object,
    printedArray:Object,
    roles:Object,
});

const { lines} = toRefs(props);

watch(() => props.lines, calculateSum());

const newArray = computed(() => {
    // Create a dictionary to store cumulative quantities for each item_no
    const cumulativeQty = {};

    // Iterate through additionalData to calculate cumulative quantities
    props.lines.forEach((item) => {
        const itemNo = item.item_no;
        const qty = parseFloat(item.qty);
        cumulativeQty[itemNo] = (cumulativeQty[itemNo] || 0) + qty;
    });

    // Filter originalData based on the specified condition
    return props.OrderLines.data.filter(
    (item) => parseFloat(item.order_qty) > (cumulativeQty[item.item_no] || 0)
    );
});




let selectedItem = ref({});


const form= useForm({

    item_no:'',
    packing_vessel_id:'',
    from_vessel:props.lastVessel,
    to_vessel:props.lastVessel,
    qty:'',
    weight:'',
    packing_session_id:props.session.data.id,
    order_no:props.session.data.order_no,
    id:null,
})


watch(form.qty , () => {
    alert('here')
    autoFillWeight(form.item_no)
})


const calculateWeight=()=>{

}



const getSelectedItem = (item_no) => {
    selectedItem.value = props.OrderLines.data.find(item => item.item_no === item_no);
    if(selectedItem.value.ass_qty!=null){
        form.weight=selectedItem.value.ass_qty
        if (selectedItem.value.ass_pcs!=null)form.qty=selectedItem.value.ass_pcs
    }
    else
    {
        if (selectedItem.value.ass_pcs!=null)form.qty=selectedItem.value.ass_pcs
        else
        if (selectedItem.value.order_qty!=null)form.qty= getItemOrderQty(selectedItem.value.item_no)-getItemPackedQty(selectedItem.value.item_no)

        if (selectedItem.value.qty_base!=null)form.weight=selectedItem.value.qty_base-getItemPackedWt(selectedItem.value.item_no)


    }
    //   console.log(selectedItem.value)
};





const getItemDescription=(itemNo)=> {
    // Filter the array based on matching from_vessel and to_vessel
    const filteredData = props.OrderLines.data.filter(item => item.item_no ===itemNo);
    // console.log(filteredData)

    // Calculate the sum of packed_qty for the filtered items
    const desc= filteredData[0].item_desc

    return desc
}

const getItemPackedQty=(itemNo)=> {
    const filteredData = props.lines.filter(item => item.item_no ===itemNo);
    if (filteredData.length>0) return filteredData[0].qty; else return 0;

}
const getItemPackedWt=(itemNo)=> {
    const filteredData = props.lines.filter(item => item.item_no ===itemNo);
    if (filteredData.length>0) return filteredData[0].weight; else return 0;

}


const getItemOrderQty=(itemNo='')=> {
    // Filter the array based on matching from_vessel and to_vessel
    const filteredData = props.OrderLines.data.filter(item => item.item_no ===itemNo);
    // console.log(filteredData)
    if (filteredData.length>0) return filteredData[0].order_qty; else return 0;

}

const getItemQtyPer=(itemNo='')=> {
    // Filter the array based on matching from_vessel and to_vessel
    const filteredData = props.OrderLines.data.filter(item => item.item_no ===itemNo);
    // console.log(filteredData)
    if (filteredData.length>0) return filteredData[0].order_qty/filteredData[0].qty_base; else return 0;

}


const createOrUpdatesession=()=>{

    if (mode.state=='Create')
    form.post(route('packingSessionLine.store'),
    {

        onSuccess:()=>{
            form.reset();
            Swal.fire(`Line ${mode.state}ed Successfully!`,'','success');
            selectedItem.value=''
            location.reload()
        }
    }
    )
    else
    form.patch(route('packingSessionLine.update',{'packingSessionLine':form.id}),
    {
        onSuccess:()=>{ form.reset();
            Swal.fire(`Lines ${mode.state}ed Successfully!`,'','success');
            // resultArray.value = Object.values(groupedData);
            window.location.reload();
        }
    });
    showModal.value=false;


}



let mode= { state: 'Create' };



let showModal=ref(false);


const showCreateModal=()=>{
    //   selectedItem.value=null
    mode.state='Create'
    form.reset();
    showModal.value=true

}

const showUpdateModal=(line)=>{

    mode.state='Update'
    form.vessel=line.vessel
    form.item_no=line.item_no
    form.from_vessel=line.from_vessel
    form.to_vessel=line.to_vessel
    form.qty=line.qty
    form.weight=line.weight
    form.order_no=line.order_no
    form.part=line.part
    form.packing_session_id=props.session.data.id
    form.id=line.id
    showModal.value=true

}




const groupedData = computed(() => {
    return props.lines.reduce((result, currentItem) => {
        const key = `${currentItem.from_vessel}-${currentItem.to_vessel}-${currentItem.packing_vessel.code}-${currentItem.packing_vessel.tare_weight}`;

        if (!result[key]) {
            result[key] = {
                from_vessel: currentItem.from_vessel,
                to_vessel: currentItem.to_vessel,
                packing_vessel_code: currentItem.packing_vessel.code,
                tare_weight: currentItem.packing_vessel.tare_weight,
                total_weight: 0
            };
        }

        result[key].total_weight += parseFloat(currentItem.weight);

        return result;
    }, {});
});


const resultArray=computed(()=>Object.values(groupedData));









</script>


<template>
    <Head title="Packing Session"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-indigo-400">
                {{ session.order }}

            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>
                                    <Button
                                    label="New"
                                    icon="pi pi-plus"
                                    severity="success"
                                    :disabled="session.data.system_entry==0"
                                    @click="showCreateModal()"
                                    rounded
                                    ></Button>
                                </template>
                                <template #center>

                                    <div class="font-bold tracking-wide">
                                        {{ session.data.order.order_no }}|
                                        {{ session.data.order.shp_name }}|
                                        {{ session.data.part }}
                                    </div>

                                </template>

                                <template #end>
                                </template>
                            </Toolbar>
                            <div v-if="lines.length==0" class="w-full p-3 mt-2 text-center">
                                No Lines were found.
                            </div>
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg" v-else>
                                <div class="grid p-5 m-2 sm:grid-cols-1 md:grid-cols-2">

                                    <div>
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                <tr class="bg-slate-300">
                                                    <!-- <th scope="col" class="px-2 py-1">
                                                        Barcode
                                                    </th> -->
                                                    <th  class="px-2 py-1">
                                                        Item
                                                    </th>

                                                    <th scope="col" class="px-2 py-1">
                                                        Order Qty
                                                    </th>
                                                    <th scope="col" class="px-2 py-1">
                                                        Weight
                                                    </th>
                                                    <th scope="col" class="px-2 py-1">
                                                        Vessel
                                                    </th>
                                                    <th scope="col" class="px-2 py-1">
                                                        From
                                                    </th>
                                                    <th scope="col" class="px-2 py-1">
                                                        To
                                                    </th>
                                                    <th scope="col" class="px-2 py-1">
                                                        Actions
                                                    </th>



                                                </tr>
                                            </thead>


                                            <tbody>
                                                <tr v-for="line in lines" :key="line.id"
                                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                <td class="text-xs">
                                                    {{ getItemDescription(line.item_no) }}
                                                </td>

                                                <td class="px-3 text-xs font-bold text-center ">
                                                    {{ getItemOrderQty(line.item_no)}}
                                                </td>
                                                <td class="px-3 py-2 text-xs font-bold">
                                                    {{ line.weight}}
                                                </td>
                                                <td class="px-3 text-xs font-bold">
                                                    {{ line.packing_vessel.code }}
                                                </td>
                                                <td class="px-3 text-xs font-bold">
                                                    {{ line.from_vessel}}
                                                </td>
                                                <td class="px-3 text-xs font-bold">
                                                    {{ line.to_vessel}}
                                                </td>
                                                <td class="flex flex-row px-3 mr-5 text-xs"  v-if="(session.data.system_entry!=0)&&(!checkMatchingRanges({'from_vessel':line.from_vessel,'to_vessel':line.to_vessel}))">

                                                    <!-- <Drop  :drop-route="route('packingSessionLine.destroy',{'id':line.id})" /> -->

                                                    <Button icon="pi pi-times" class="justify-end p-button-danger"
                                                    text rounded
                                                    @click="drop(route('packingSessionLine.destroy',{'id':line.id}))"
                                                    />
                                                    <Button
                                                    icon="pi pi-pencil"
                                                    severity="info"
                                                    text
                                                    @click="showUpdateModal(line)"
                                                    />

                                                </td>
                                                <td v-else></td>

                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="items-center p-3 text-center rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr class="">
                                                <th>Vessel Range</th>
                                                <th>Vessel Count</th>

                                                <th>Vessel</th>
                                                <th>Tare Weight</th>
                                                <th>Weight</th>
                                                <th>Label</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(sum, index) in calculatedSum" :key="index" class="text-center bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                                <td>{{ sum.from_vessel +'-'+sum.to_vessel }}</td>
                                                <td>{{ sum.to_vessel-sum.from_vessel+1 }}</td>
                                                <td>{{ sum.packing_vessel_id }}</td>
                                                <td>{{ sum.tare_weight*(sum.to_vessel-sum.from_vessel+1) }}</td>
                                                <td>{{ sum.weight }}</td>
                                                <td> <Button icon="pi pi-print" severity="success"
                                                    :disabled="checkMatchingRanges(sum)"
                                                    @click="generatePDF(sum.from_vessel,
                                                    sum.to_vessel,
                                                    sum.packing_vessel_id,
                                                    (sum.tare_weight*(sum.to_vessel-sum.from_vessel+1)+sum.weight)/(sum.to_vessel-sum.from_vessel+1))"
                                                    /> </td>
                                                </tr>
                                            </tbody>
                                            <!-- Table footer with totals -->
                                            <tr class="font-bold text-center bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                                <td >Total</td>
                                                <td>{{ calculateTotals[2] }}</td>
                                                <td></td>

                                                <td></td>
                                                <td>{{ calculateTotals[0] }}</td>

                                            </tr>

                                        </table>


                                    </div>

                                </div>
                            </div>

                            <Toolbar>
                                <template #center>

                                    <Button
                                    v-if="session.data.system_entry==1"
                                    label="End Packing"
                                    severity="warning"
                                    @click="closePacking()"
                                    />

                                    <Link v-else :href="route('packingSession.index')">
                                        <Button

                                        label="Back"
                                        severity="info"

                                        />
                                    </Link>
                                </template>
                            </Toolbar>


                        </div>




                        <!--end of stats bar-->

                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="showModal=false" >

            <div class="flex flex-col p-4 rounded-sm">

                <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Packing Line</div>
                <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update session</div> -->

                <form  @submit.prevent="createOrUpdatesession()">

                    <div class="flex flex-col justify-center gap-3">

                        <Dropdown
                        v-if="mode.state=='Create'"
                        v-model="form.item_no"
                        :options="newArray"
                        optionLabel="item_description"
                        optionValue="item_no"
                        filter=""
                        placeholder="Select Item"
                        @change="getSelectedItem(form.item_no)"
                        />
                        <div v-else>
                            {{getItemDescription(form.item_no)}}
                        </div>

                        <div class="p-3 text-black bg-slate-200 " v-show="selectedItem!==null">
                            <div class="p-2 text-center text-white bg-lime-700"> Order Qty : {{ (mode.state=='Create')?selectedItem.order_qty: getItemOrderQty(form.item_no) }}</div>
                            <div class="p-2 text-center text-white bg-slate-500">Packed Qty : {{  getItemPackedQty(form.item_no) }} </div>
                        </div>
                        <div class="flex flex-row">
                            <span class="mt-4 p-float-label">
                                <InputText
                                v-show="selectedItem!==''"
                                @change="validateQty(form.item_no)"
                                v-model="form.qty"

                                />
                                <label for="Qty">Qty</label>
                            </span>


                            <span class="mt-4 p-float-label">
                                <InputText id="weight"
                                v-model="form.weight"
                                placeholder="Weight"
                                @change="validateWeight(form.item_no)"


                                />
                                <label for="weight">Weight</label>
                            </span>

                            <span class="mt-4 p-float-label">


                                <InputText
                                placeholder="Weight Calculator"
                                v-model="calc"
                                @change="evaluateExpression()"

                                />
                                <label for="Weight Calculator">Weight Calculator</label>
                            </span>
                        </div>
                        <div>
                            <div class="flex flex-row gap-3 my-1 ">

                                <span class="mt-6 p-float-label">
                                    <Dropdown
                                    v-model="form.packing_vessel_id"
                                    :options="props.packingVessels.data"
                                    option-label="code"
                                    option-value="id"
                                    placeholder="Select Vessel"
                                    />
                                    <label for="Vessel">Vessel</label>
                                </span>



                                <span class="mt-6 p-float-label">

                                    <InputText

                                    v-model="form.from_vessel"
                                    />
                                    <label for="From Vessel">From Vessel</label>
                                </span>

                                <span class="mt-6 p-float-label">


                                    <InputText

                                    placeholder="To Vessel"
                                    v-model="form.to_vessel"
                                    />
                                    <label for="To Vessel">To Vessel</label>
                                </span>

                            </div>

                        </div>




                        <Button
                        severity="info"
                        type="submit"
                        :label=mode.state
                        :disabled="form.item_no==''||form.vessel==''||form.qty==''||form.weight==''||form.from_vessel==''||form.to_vessel==''||form.processing||checkMatchingRanges({'from_vessel':form.from_vessel,'to_vessel':form.to_vessel})"

                        />
                        <Button label="Cancel" severity="warning" icon="pi pi-cancel" @click="showModal=false"/>
                    </div>

                </form>

            </div>

        </Modal>
    </AuthenticatedLayout>

</template>
<style lang="scss" scoped>

</style>
