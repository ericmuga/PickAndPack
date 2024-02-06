



<script setup>


import SearchBox from '@/Components/SearchBox.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'


const form= useForm({
     'company':'FCL',
     'received_date':'',
     'shp_date':'',
    'cust_no':''
})





const createOrUpdateItem=()=>{
    if (mode.state=='Create')
          form.get(route('fetch'),
        {
            onSuccess: () =>{
                Swal.fire('Success!','Posted Successfully','success')

                // form.reset('password')
            }
        })



}


let mode= { state: 'Create' };

//   defineProps({
//        items:Object
//   })

  let showModal=ref(false);


const showCreateModal=()=>{

    mode.state='Create'
   form.reset();
    showModal.value=true

}

onMounted(()=>{
   setInterval(() => {

      form.get(route('fetch'),
        {
            onSuccess: () =>{
                Swal.fire('Success!','Posted Successfully','success')

                // form.reset('password')
            }
        })


   }, 3000000);
})

// const showUpdateModal=(item)=>{

//     mode.state='Update'
//     // alert(mode.state)

//     form.barcode=item.barcode
//     form.item_no=item.item_no
//     form.description=item.description
//     form.posting_group=item.posting_group
//     showModal.value=true
// }
</script>


<template>
    <Head title="API"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">API</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>
                                    <!-- <Button label="New" icon="pi pi-plus" class="mr-2" />
                                        <Button label="Upload" icon="pi pi-upload" class="p-button-success" /> -->
                                        <!-- <i class="mr-2 pi pi-bars p-toolbar-separator" /> -->
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="items" class="p-button-warning"></SplitButton> -->
                                    <Button
                                         label="Add"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
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

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} API</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update Item</div> -->

          <form  @submit.prevent="createOrUpdateItem()">

<div class="flex flex-col justify-center gap-3">


        <label for="">'Received Date'</label><input type="date" v-model="form.received_date"/>
        <InputText
           placeholder="Company"
           v-model="form.company"

        />
        <label for="">Shipment Date</label><input type="date" v-model="form.shp_date"/>

        <InputText
           placeholder="Customer"
           v-model="form.cust_no"
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
<style lang="scss" scoped>

</style>
