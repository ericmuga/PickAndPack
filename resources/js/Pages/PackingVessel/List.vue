
<script setup>
  import SearchBox from '@/Components/SearchBox.vue'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';

import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'

import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'


const form= useForm({
    code:'',
    description:'',
    tare_weight:'',
    blocked:false
})





const createOrUpdatepackingVessel=()=>{
    if (mode.state=='Create')
          form.post(route('packingVessel.store'),{

                onSuccess:()=>{ Swal.fire(`Packing Vessel ${mode.state}ed Successfully!`,'','success');}

                });
        else
     form.patch(route('packingVessel.update',form.packingVessel_no),{
                    onSuccess:()=>{ Swal.fire(`Packing Vessel ${mode.state}ed Successfully!`,'','success');}
                    })
      showModal.value=false;


}


let mode= { state: 'Create' };

 const prop= defineProps({
                            packingVessels:Object
                        });

  let showModal=ref(false);


const showCreateModal=()=>{
    form.reset();
    mode.state='Create'

    showModal.value=true

}

const showUpdateModal=(packingVessel)=>{

    mode.state='Update'
    // alert(mode.state)

    form.code=packingVessel.code
    form.description=packingVessel.description
    form.tare_weight=packingVessel.tare_weight
    form.blocked=packingVessel.blocked
    showModal.value=true
}
</script>


<template>
    <Head title="Packing Vessels"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Packing Vessels {{ packingVessels.data.length }}</h2>
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
                                         label="Add"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>
                                <template #center>
                                    <div>
                                        <Pagination :links="packingVessels.meta.links" />
                                    </div>


                                </template>

                                    <template #end>




                                             <SearchBox model="packingVessel.index" />
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
                                                           Code
                                                        </th>

                                                        <th scope="col" class="px-6 py-3">
                                                            Description
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Tare Weight
                                                        </th>
                                                         <th scope="col" class="px-6 py-3">
                                                            Blocked
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Actions
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="packingVessel in packingVessels.data" :key="packingVessel.id"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ packingVessel.code }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ packingVessel.description }}
                                                    </td>
                                                     <td class="px-3 py-2 text-xs font-bold">
                                                        {{ packingVessel.tare_weight }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs">

                                                            {{packingVessel.blocked?'Yes':'No'}}

                                                    </td>
                                                    <td>
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('packingVessel.destroy',{'id':packingVessel.code})"/>
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text


                                                                      @click="showUpdateModal(packingVessel)"
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
                                <Pagination :links="packingVessels.meta.links" />
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

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Packing Vessel</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update packingVessel</div> -->

          <form  @submit.prevent="createOrUpdatepackingVessel()">

<div class="flex flex-col justify-center gap-3">


        <InputText
           placeholder="Code"
           :disabled="mode.state=='Update'"
           v-model="form.code"
        />
        <InputText
           placeholder="Description"
           v-model="form.description"
        />
        <InputNumber
           placeholder="Tare Weight"
           v-model="form.tare_weight"
           :minFractionDigits="1" :maxFractionDigits="2"
        />

        <label> Blocked?</label>
        <input type="checkbox"
           placeholder="Blocked"
           v-model="form.blocked"
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
