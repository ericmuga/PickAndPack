



<script setup>
import SearchBox from '@/Components/SearchBox.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { useForm } from '@inertiajs/inertia-vue3'
import {ref} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'


const form= useForm({
    plate:'',
    fleet_no:'',
    tare_weight:'',
    load_capacity:'',
    fuel_capacity:'',
    status:'',
    make:''

})





const createOrUpdatevehicle=()=>{
    if (mode.state=='Create')
          form.post(route('vehicles.store'),
                    { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`Vehicle ${mode.state}d Successfully!`,'','success');
                                    }
                    }
                   )
        else
     form.patch(route('vehicles.update',form.plate),
                { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`Vehicle ${mode.state}d Successfully!`,'','success');
                                    }
                    })
      showModal.value=false;


}


let mode= { state: 'Create' };

const props=  defineProps({
       vehicles:Object,
       role:Object,
       permissions:Object
  })

  let showModal=ref(false);


const showCreateModal=()=>{
    form.reset();
    mode.state='Create'

    showModal.value=true

}

const showUpdateModal=(vehicle)=>{
// alert('here');
    mode.state='Update'
    form.plate=vehicle.plate;
    form.fleet_no=vehicle.fleet_no;
    form.tare_weight=vehicle.tare_weight;
    form.load_capacity=vehicle.load_capacity;
    form.fuel_capacity=vehicle.fuel_capacity;
    form.status=(vehicle.status==1)?true:false
    form.make=vehicle.make;
    showModal.value=true;
}
</script>


<template>
    <Head title="Vehicles"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Vehicle </h2>
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
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="vehicles" class="p-button-warning"></SplitButton> -->
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
                                        <Pagination :links="vehicles.meta.links" />
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>


                                        <a :href="route('vehicles.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="vehicles"/>
                                        </a>




                                             <SearchBox :model="vehicles.index" />
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
                                                           Plate
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Fleet No.
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Make
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Tare Weight (KG)
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Load Capacity (KG)
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Fuel Capacity (L)
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                          Active
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="vehicle in vehicles.data" :key="vehicle.id"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ vehicle.plate }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ vehicle.fleet_no }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ vehicle.make }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ vehicle.tare_weight }}
                                                    </td>


                                                    <td class="px-3 py-2 text-xs">

                                                            {{vehicle.load_capacity}}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{vehicle.fuel_capacity}}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{(vehicle.status==1)?'Yes':'No'}}

                                                    </td>
                                                    <td>
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('vehicles.destroy',{'vehicle':vehicle.id})"/>
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text
                                                                      @click="showUpdateModal(vehicle)"
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
                                <Pagination :links="vehicles.meta.links" />
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

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Vehicle</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update vehicle</div> -->

          <form  @submit.prevent="createOrUpdatevehicle()">

<div class="flex flex-col justify-center gap-3">


        <InputText
           placeholder="Plate No."

           v-model="form.plate"
        />
        <InputText
           placeholder="Fleet No."
           v-model="form.fleet_no"

        />
        <InputText
           placeholder="Make"

           v-model="form.make"
        />

         <InputText
           placeholder="Tare Weight (KG)"
           v-model="form.tare_weight"

        />
         <InputText
           placeholder="Load Capacity (KG)"
           v-model="form.load_capacity"

        />

         <InputText
           placeholder="Fuel Capacity (L)"
           v-model="form.fuel_capacity"

        />
        <div class="items-center w-full space-x-3 text-center">
             <span>Active?</span>
       <input type="checkbox"     v-model="form.status"    />
        </div>




        <Button
          severity="info"
          type="submit"
          :label=mode.state
          :disabled="form.processing"

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
