



<script setup>
  import SearchBox from '@/Components/SearchBox.vue'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
// import Button from 'primevue/button';
// import MultiSelect from 'primevue/multiselect';
// import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
// import FilterPane from '@/Components/FilterPane.vue'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'
// import Route from 'vendor/tightenco/ziggy/src/js/Route';

const form= useForm({
     'item_no':'',
     'description':'',
     'barcode':'',
    'posting_group':''
})

const createOrUpdateItem=()=>{
    if (mode.state=='Create')
          form.post(route('items.store'))
        else
     form.patch(route('items.update',form.item_no))
      showModal.value=false;
    Swal.fire(`Item ${mode.state}ed Successfully!`,'','success');

}


let mode= { state: 'Create' };

  defineProps({
       items:Object
  })

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
</script>


<template>
    <Head title="Items"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Items {{ items.data.length }}</h2>
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
                                <template #center>
                                    <div>
                                        <Pagination :links="items.meta.links" />
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>
                                        <a :href="route('items.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="Items"/>
                                        </a>




                                             <SearchBox model="items.index" />
                                    </template>
                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                           Item No.
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            BarCode
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Description
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Prepacks
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Actions
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="item in items.data" :key="item.item_no"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ item.item_no }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ item.barcode }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.description }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs">

                                                            {{item.prepacks.length}}

                                                    </td>
                                                    <td>
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('items.destroy',{'item':item.item_no})"/>
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text


                                                                      @click="showUpdateModal(item)"
                                                                      />
                                                       </div>
                                                    </td>

                                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <Pagination :links="items.meta.links" />
                            </div>
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
<style lang="scss" scoped>

</style>
