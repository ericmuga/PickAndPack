<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';
import Pagination from '@/Components/Pagination.vue';
import { useForm } from '@inertiajs/inertia-vue3'
import Modal from '@/Components/Modal.vue'


const search=ref()
watch(search, debounce(()=>{Inertia.post('/order/all',{search:search.value}, {preserveScroll: true})}, 500));

const prop=defineProps({
    sessions:Object,
    drivers:Object,
    vehicles:Object,
})
    const inputField=ref(null);

    onMounted(() => {
    inputField.value.focus();
});

//   let showModal=ref(false);
let newItem=ref('');

watch( newItem,
debounce( ()=>{Inertia.get(route('packing.index'),{'search':newItem.value})})
,500);



const form= useForm({
     'vehicle_id':'',
     'assistant_loader_id':'',
     'driver_id':'',
    //  'load_array':[],

})





const createOrUpdateItem=()=>{
    if (mode.state=='Create')
          form.post(route('loadingSession.store'))
        else
     form.patch(route('loadingSession.update',form.id))
      showModal.value=false;
    Swal.fire(`Load ${mode.state}ed Successfully!`,'','success');

}


let mode= { state: 'Create' };


  let showModal=ref(false);


const showCreateModal=()=>{

    mode.state='Create'
    form.barcode=''
    form.item_no=''
    form.description=''
    form.posting_group=''
    showModal.value=true

}

const showUpdateModal=(item)=>{

    mode.state='Update'
    // alert(mode.state)

    form.barcode=item.barcode
    form.item_no=item.item_no
    form.description=item.description
    form.posting_group=item.posting_group
    showModal.value=true
}


// const confirmPack=(order_no,part)=>{ Inertia.get(route('pack.order',{'order_no':order_no,'part_no':part}))}









</script>


<template>
    <Head title="Loading"/>

    <AuthenticatedLayout @add="showModal=true">
          <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Loading</h2>
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
                                      <Button
                                         label="Add"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>

                                <template #center>
                                    <div>
                                        <!-- <Pagination :links="orderLines.meta.links" /> -->
                                        <input type="text" v-model="newItem"  ref="inputField" class="m-2 rounded-lg bg-slate-300 text-md">

                                        <!-- <SearchBox :model="route('order.pack')" /> -->
                                    <div>
                                        <!-- <Pagination :links="orders.meta.links" /> -->
                                    </div>
                                    </div>


                                </template>

                                    <template #end>




                                            <!-- <InputText v-model="search" aria-placeholder="search"/> -->

                                             <div>

                                            </div>
                                            </template>
                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

<!---table comes here-->



                                        </div>






                </div>
                        <div class="items-center w-full text-center">

                            <Pagination :links="sessions.meta.links" />
                        </div>



                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" >

     <div class="flex flex-col p-4 rounded-sm">

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Item</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update Item</div> -->

          <form  @submit.prevent="createOrUpdateItem()">

<div class="flex flex-col justify-center gap-3">


        <InputText
           placeholder="Item No"
           :disabled="mode.state=='Update'"
           v-model="form.item_no"
        />
        <InputText
           placeholder="Item Description"
           v-model="form.description"
        />
        <InputText
           placeholder="Item Barcode"
           v-model="form.barcode"
        />
        <InputText
           placeholder="Item Posting Group"
           v-model="form.posting_group"
        />
        <Button
          severity="info"
          type="submit"
          :label=mode.state

        />
        <Button label="Cancel" severity="warning" icon="pi pi-cancel" @click="showModal=false"/>
</div>

    </form>

     </div>

  </Modal>
</AuthenticatedLayout>
</template>
