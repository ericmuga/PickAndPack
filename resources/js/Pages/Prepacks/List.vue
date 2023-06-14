<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Modal from '@/Components/Modal.vue'
import Button from 'primevue/button';
import { useForm } from '@inertiajs/inertia-vue3';

import {watch, ref} from 'vue';



const form= useForm({
     'name':'',
     'item_no':'',
     'pack_size':'',
     'isActive':false,
})

const createPrepack=()=>{
    form.post(route('prepacks.store'))
// close modal
// showModal=false;

}
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
                                        <Button label="New" icon="pi pi-plus" class="mr-2"  @click="showModal=true" />
                                        <!-- <Button label="Upload" icon="pi pi-upload" class="p-button-success" /> -->
                                        <!-- <i class="mr-2 pi pi-bars p-toolbar-separator" /> -->
                                    <!-- <SplitButton label="Save" icon="pi pi-check" :model="items" class="p-button-warning"></SplitButton> -->
                                </template>
                                <template #center>


                                </template>


                                    <!-- <Button
                                         icon="pi pi-plus"
                                         label="Add"

                                         rounded
                                    ></Button> -->



                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                            Barcode
                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                            Prepack Name
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 ">
                                                            Item No
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Item Description
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Pack Size
                                                        </th>

                                                        <th scope="col" class="px-6 py-3">
                                                            Active
                                                        </th>




                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="item in prepacks" :key="item.prepack_name"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ item.name }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ item.item_no }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.description}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.pack_size}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        <Checkbox v-model="checked" :binary="true" :checked="item.isActive" disabled="true" />
                                                    </td>



                                            </tr>

                            </tbody>
                        </table>
                    </div>



                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" :errors="errors">
    <form @submit.prevent="createPrepack()" >
        <!-- <div v-if="errors=0"> -->
          <ul class="text-xs text-red-500">
            <li v-for="error in errors">{{ error.message  }}</li>
          </ul>
    <!-- </div> -->
       <div class="w-full p-4 font-bold text-center text-white bg-slate-600"> Create Prepack</div>
       <div class="flex flex-col justify-center gap-2 p-5">


        <InputText
           v-model="form.name"
           placeholder="Prepack Name"
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


        <Button type="submit" label="Add" icon="pi pi_plus" class="icon-left" />
</div>

    </form>

  </Modal>
</AuthenticatedLayout>

</template>
