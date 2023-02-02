<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/inertia-vue3';
import StatsTile from '@/Components/StatsTile.vue';
import { Link } from '@inertiajs/inertia-vue3'
import { computed, onMounted, reactive, ref,watch } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import debounce from 'lodash/debounce';
import pickBy from 'lodash/debounce';
import Dropdown from 'primevue/dropdown';
import MultiSelect from 'primevue/multiselect';
import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';
import Swal from 'sweetalert2'
import InputNumber from 'primevue/inputnumber'
import { Inertia } from '@inertiajs/inertia';

const props=defineProps({
    order:Object,
});

const ExecutedArray =reactive([]);


let scannedItem=ref({})

const itemArray=ref(props.order.items);

const setSelectedItem=(id)=>scannedItem=id;

let viewItem= ref('')
let activeIndex=-1;

const form=useForm({ id:'',
                    executedQty:'',
                    batchString:'',
                    orderNo:props.order.No,
                    status:'',
                    name:'',
                    Oqty:'',
                    chiller:''
                    })

watch(scannedItem, function(scannedItem){
    if (scannedItem!='')
    {
        form.id=scannedItem.id
        form.name=scannedItem.name
        form.Oqty=scannedItem.Qty
    }


} );
const executeItem=()=>{
    //append item to list
        //let index=0;
        let item={
                            id:form.id,
                            name:form.name.substring(0,form.name.indexOf('|')),
                            chiller:form.chiller.code,
                            Oqty:form.Oqty,
                            executedQty:form.executedQty,
                            batchString:form.batchString,
                            orderNo:props.order.No,
                            status:'Executed'

                        };

       let index =ExecutedArray.map(obj=>obj.id).indexOf(form.id)

        if(index==-1)ExecutedArray.push(item);
        else ExecutedArray.splice(index,1,item);




         form.reset();
         scannedItem.value=''
         activeIndex=0;

    }

const closeOrder=()=>{
    Inertia.post(route('order.execute'),ExecutedArray,{'preserveScroll':true});
    Swal.fire({
            icon: 'success',
            text: 'Saved',

        })
}




</script>

<template>
    <Head :title="PickOrder" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{order.No }} | {{order.Part}} | {{ order.Customer }}</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->
                        <div class=" md:grid md:grid-cols-2 md:space-x-1 w-full items-center justify-between md:gap-1  sm:space-y-2 md:flex-row">

                            <div class="border-r-1 border-gray-400 p-3 flex flex-col ">
                                <Dropdown v-model="scannedItem"
                                :options="order.items"
                                optionLabel="name" :filter="true"
                                placeholder="Select Item"
                                :showClear="true"
                                :optionValue="id"
                                >
                            </Dropdown>
                        </div>
                        <div v-if="scannedItem.id"
                        class="border-r-1 border-gray-400 p-3 flex flex-col ">
                        <form action="" @submit.prevent="executeItem()">
                            <table class="text-center">
                                <tr class="flex justify-center my-3 bg-teal-600 text-white border border-2 rounded-md"><td class="text-bold">Order Qty:</td><td>{{scannedItem.Qty }}</td></tr>
                                <spaced-rule/>
                                <tr class="flex justify-center my-3"><td class="text-bold">Special Instruction :</td><td>{{scannedItem.Comment }}</td></tr>
                                <spaced-rule/>
                                <tr class="flex justify-between my-3 flex-col">

                                    <td class="text-xs">
                                        Chiller
                                    </td>
                                    <td>
                                        <Dropdown v-model="form.chiller"
                                        :options="order.chillers"
                                        optionLabel="code" :filter="true"
                                        placeholder="Select Chiller"
                                        :showClear="true"
                                        :optionValue="code"
                                        >
                                    </Dropdown>
                                </td>
                                <td>Executed</td>
                                <td>
                                    <InputNumber  v-model="form.executedQty" mode="decimal" :minFractionDigits="1" />
                                </td>

                                <InputText v-model="form.id" :hidden="true" :modelValue="scannedItem.id" />
                                <InputText v-model="form.name" :hidden="true" :modelValue="scannedItem.name" />
                                <InputText v-model="form.Oqty" :hidden="true" :modelValue="scannedItem.Qty" />


                                <!-- <InputText  v-model="form.batchString"/> -->
                                <td>Batch No.</td>
                                <td>
                                    <MultiSelect v-model="form.batchString"
                                    :options="order.batches"
                                    optionLabel="code" :filter="true"
                                    placeholder="Select Batch"
                                    :showClear="true"
                                    :optionValue="code"
                                    >
                                </MultiSelect>

                            </td>
                        </tr>
                        <tr class="w-full items-center">
                            <Button label="Execute" type="submit" class="p-button-rounded p-button-success" />

                        </tr>
                    </table>
                </form>
            </div>


        </div>
        <!--end of stats bar-->
        <div class="flex justify-center" v-show="activeIndex==0">
            <Accordion  :activeIndex= activeIndex>
                <AccordionTab header="Order Summary"  >
                    <table class="text-xs">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">


                            <tr>
                                <th class="border">Item</th> <th class="border">Order Qty</th> <th class="border">Exec. Qty</th><th class="border">Batch No</th> <th class="border">Chiller</th>
                            </tr>
                        </thead>
                        <tr  v-if="ExecutedArray.length>0" v-for="item in ExecutedArray" class="bg-white  dark:bg-gray-800 dark:border-gray-700 text-center" >
                            <td class="border">{{ item.name}}</td>
                            <td class="border">{{ item.Oqty }}</td>
                            <td class="border">{{ item.executedQty }}</td>
                            <td class="border"><span v-if="item.batchString.length>0" v-for=" bstring in item.batchString" :key="code">{{bstring.code}}{{ item.batchString[item.batchString.length-1].code===bstring.code?'':'|' }}</span></td>
                            <td class="border">{{ item.chiller }}</td>

                        </tr>
                    </table>

                    <div v-if="order.items.length==ExecutedArray.length" class="w-full text-center items-center flex-auto m-5">
                        <Button type="text" class="p-button-rounded p-button-success" label="Close" @click="closeOrder()" />
                    </div>
                </AccordionTab>

            </Accordion>
        </div>
    </div>
</div>
</div>
</div>
</AuthenticatedLayout>
</template>
