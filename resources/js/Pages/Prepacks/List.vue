<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Modal from '@/Components/Modal.vue'
import Button from 'primevue/button';
import { useForm } from '@inertiajs/inertia-vue3';
import Swal from 'sweetalert2'
import Drop from '@/Components/Drop.vue'
import InputSwitch from 'primevue/inputswitch';
import {watch, ref,onMounted} from 'vue';
import SearchBox from '@/Components/SearchBox.vue'

const form= useForm({
     'prepack_name':'',
     'item_no':'',
     'pack_size':'',
     'isActive':false,
})



let mode= { state: 'Create' };


onMounted( ()=>{
 console.log(props)
    if (props.errors?.hasOwnProperty('success'))
     {
        if (props.errors.success!='')
         Swal.fire('Success!',props.errors.success,'')
        props.errors.success=''
     }
})




const createOrUpdatePrepack=()=>{
    form.prepack_name=form.item_no+'|'+form.pack_size
    if (mode.state=='Create')
          form.post(route('prepacks.store'))
        else
     form.patch(route('prepacks.update',form.item_no))
      showModal.value=false;
    Swal.fire(`Prepack ${mode.state}ed Successfully!`,'','success');

}


const showCreateModal=()=>{

    mode.state='Create'
    form.prepack_name=''
    form.item_no=''
    form.pack_size=''
    form.isActive=''
    showModal.value=true

}

const showUpdateModal=(item)=>{

    mode.state='Update'
    // alert(mode.state)

    form.prepack_name=item.prepack_name
    form.item_no=item.item_no
    form.pack_size=item.pack_size
    form.isActive=item.isActive
    showModal.value=true
}


// const logObject=()=>{console.log(props.prepacks)}






const props= defineProps({
    prepacks:Object,
    items:Object,
})


let showModal=ref(false);

</script>

<template>
    <Head title="Prepacks"/>

    <AuthenticatedLayout @add="showModal=true">

        <div class="py-6">
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

                                    <!-- <Button
                                         label="Log"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="logObject()"
                                         rounded
                                    ></Button> -->
                                </template>
                                <template #center>

                                     <Pagination :links="prepacks.meta.links" />
                                </template>

                                <template #end>
                                    <SearchBox model="prepacks.index" />
                                </template>
                                </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="text-center bg-slate-300">


                                                        <th scope="col" class="px-6 py-3 ">
                                                          Prepack No.
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Item Description
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Pack Size
                                                        </th>

                                                        <th scope="col" class="px-6 py-3">
                                                            Order Lines
                                                        </th>

                                                        <th scope="col" class="px-6 py-3">
                                                            Active
                                                        </th>

                                                        <th class="text-left">
                                                            Actions
                                                        </th>




                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="item in prepacks.data" :key="item.prepack_name"
                                                    class="text-center bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ item.prepack_name }}
                                                    </td>

                                                    <!-- <td class="px-3 py-2 text-xs">
                                                        {{ item.item_no }}
                                                    </td> -->
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.item.description}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.pack_size}}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.linePrepack_count}}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold">

                                                        <!-- <InputSwitch v-model="checked" disabled /> -->
                                                        <div v-if="item.isActive==1" >
                                                           <p>Yes</p>
                                                        </div>
                                                        <div v-else>
                                                            <p>No</p>
                                                            <!-- <Button icon="pi pi-times" disabled severity="danger" text rounded aria-label="Cancel" /> -->
                                                        </div>

                                                    </td>
                                                    <td>
                                                        <div class="flex flex-row" >
                                                          <Drop  :drop-route="route('prepacks.destroy',{'prepack':item.prepack_name})"/>
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
                    <toolbar>
                    <template #center>
                        <Pagination :links="prepacks.meta.links" />
                        <!-- <SearchBox model="prepacks.index" /> -->
                    </template>
                    </Toolbar>



                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" :errors="errors">
    <form @submit.prevent="createOrUpdatePrepack()" >
        <!-- <div v-if="errors=0"> -->
          <ul class="text-xs text-red-500">
            <li v-for="error in errors">{{ error.message  }}</li>
          </ul>
    <!-- </div> -->
       <div class="w-full p-4 font-bold text-center text-white bg-slate-600">{{ mode.state }} Prepack</div>
       <div class="flex flex-col justify-center gap-2 p-5">


        <InputText
           v-model="form.prepack_name"
           placeholder="Prepack Name"
           :disabled="mode.state=='Update'"
           hidden
        />


        <Dropdown
              v-model="form.item_no"
              :options="props.items"
              optionLabel="description"
              optionValue="item_no"
              filter

              placeholder="Select a Item"
              class="w-full md:w-14rem"
        />



         <InputText
           v-model="form.pack_size"
           placeholder="Pack_size"
        />
        <div class="text-center">
            <span class="text-xs"> Active</span>
                    <input type="checkbox" v-model="form.isActive"/>
        </div>
        <Button type="submit" :label="mode.state" severity="info" class="icon-left w-sm" />
        <Button label="Cancel" severity="warning" icon="pi pi-cancel" @click="showModal=false"/>
</div>

    </form>

  </Modal>
</AuthenticatedLayout>

</template>
