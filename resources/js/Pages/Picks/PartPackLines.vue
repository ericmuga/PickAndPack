<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
// import {debounce} from 'lodash/debounce';
import {watch, ref,onMounted, nextTick,reactive,computed} from 'vue';
import Swal from 'sweetalert2'
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce'
import ProgressBar from 'primevue/progressbar';
import { useSearchArray } from '@/Composables/useSearchArray';
import axios from 'axios';
// import { pick } from 'lodash';

const showModal=ref(false);

const props =defineProps({
    pick:Object,
    lines:Object,
})
onMounted(()=>{
    remainingArray.value=props.lines;
})

const currentItem= ref({});
const assembledArray=ref([]);

const form =ref({
                item_description:'',
                customer_spec:'',
                barcode:'',
                item_no:'',
                group_qty:'',
                assembled_qty:'',
                assembled_pcs:'',
                from_batch:'',
                to_batch:'',
                // pick_id:props.pick[0].pick_id

            });



const search=ref('');



watch(search,debounce(()=>{
    if (search.value!='')
        {
        //    console.log(filteredItems)
               currentItem.value=props.lines.filter(item =>
                                        item.barcode==search.value ||
                                        item.item_no==search.value
                                    )

               if (currentItem.value.length>0)
                    setModalItem(currentItem.value[0]);
                else alert('Item Not Found')
        }
        else
        clearModalItem();

        },500))

const setModalItem=(currentItem)=>{
    form.value.item_description=currentItem.item_description
    form.value.customer_spec=currentItem.customer_spec
    form.value.barcode=currentItem.barcode
    form.value.item_no=currentItem.item_no
    form.value.group_qty=currentItem.group_qty
    // form.pick_id=props.pick[0].pick_id
    showModal.value=true
};

const clearModalItem=()=>{
   form.value={};
    showModal.value=false;
    clearForm();
}

const clearForm=()=>{
            form.value.item_description=''
            form.value.customer_spec=''
            form.value.barcode=''
            form.value.item_no=''
            form.value.group_qty=''
            form.value.assembled_qty=''
            form.value.assembled_pcs=''
            form.value.from_batch=''
            form.value.to_batch=''

  currentItem.value={}

}
const remainingArray=ref([]);

const submitForm = () => {
  // Add the form's current value to the assembledArray
  assembledArray.value.push({ ...form.value });

  // Get all item_no values from assembledArray
  const itemNosInAssembledArray = assembledArray.value.map(i => i.item_no);

  // Update remainingArray by filtering out items that have item_no present in assembledArray
  remainingArray.value = props.lines.filter(item => !itemNosInAssembledArray.includes(item.item_no));

  // Optionally clear the form
  clearForm();
  search.value=''

  // Close the modal
  showModal.value = false;
};


const closePick=()=>{
    // console.log(assembledArray.value);
   Inertia.post(route('picks.store',{data:assembledArray.value,pick_id:props.pick.id}))


}









</script>

<template >
    <Head title="Picks"/>

    <AuthenticatedLayout >

        <div class="py-3">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <div class="w-full p-3 text-center"> <input v-model="search" class="p-2 text-center bg bg-slate-300" placeholder="Search Item" /></div>
                        <div class="grid w-full grid-cols-2 md:grid-grid-cols-2 sm:grid-cols-1">
                            <ul class="col-span-1">
                               <div class="p-2 bg bg-slate-400" v-show="remainingArray.length>0"> Ordered </div>
                            <li v-for="line in remainingArray" :key="item_no" class="p-3 border-b-2 bottom-1" @click="search=line.item_no">
                                {{ line.item_description  }}|{{ line.group_qty }} | {{ line.customer_spec }}
                            </li>
                        </ul>
                        <ul class="col-span-1">
                             <div class="p-2 bg bg-slate-400" v-show="assembledArray.length>0">Assembled </div>
                            <li v-for="line in assembledArray" :key="item_no" class="p-3 border-b-2 bottom-1">
                                {{ line.item_description  }}|{{ line.group_qty }} | {{ line.customer_spec }}
                            </li>
                        </ul>
                        </div>

                    </div>
                    <div class="w-full p-4 text-center"> <Button label="Close Pick" icon="pi pi-send" @click="closePick()"/></div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <Modal :show="showModal" @close="showModal=false" :errors="errors"> <!-- {{ dynamicModalContent  }} -->
        <!-- {{ showModal }} -->

        <div class="p-4 font-bold text-center text-white bg-slate-600">Assemble Pick</div>
        <div>

            <form @submit.prevent="submitForm()" class="flex flex-col justify-center gap-2 p-5">
                <span class="p-3 font-bold text-center capitalize bg-yellow-400 rounded-lg">{{ form.item_description }}</span>
                <span class="w-full p-2 font-bold text-black bg-orange-400 rounded-lg">Customer Spec: {{ form.customer_spec }}</span>
                <div class="grid grid-cols-2 ">
                    <div class="">
                        <span class="px-3 text-center capitalize">Grouped Order Qty:</span>
                        <span class="py-3 font-bold">{{ form.group_qty }}</span>
                    </div>

                </div>


                <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                    <div>
                        <span class="font-bold">PCS </span>
                        <InputNumber inputId="integeronly"  v-model="form.assembled_pcs"/>
                    </div>
                    <div
                    class="flex items-center space-x-2"
                    >
                    <span class="font-bold">Weight</span>
                    <InputNumber
                    inputId="minmaxfraction" :minFractionDigits="2" :maxFractionDigits="5"
                    v-model="form.assembled_qty"
                    />
                </div>
                <InputText v-model="form.from_batch"  placeholder="From Batch." />
                <InputText  v-model="form.to_batch" placeholder="To Batch." />
                <Button  label="Assemble" icon="pi pi-send" class="w-sm" severity="success"  type="submit" :disabled="form.processing" />
                <Button label="Cancel" severity="danger" icon="pi pi-cancel" @click="clearModalItem()"/>
            </div>
        </form>
    </div>
</Modal>

</template>
<style>
button:hover {
    cursor: pointer;
}

p:hover {
    cursor: pointer;
}
</style>
