



<script setup>
import Toolbar from 'primevue/toolbar';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { useForm } from '@inertiajs/inertia-vue3'
import {ref} from 'vue';
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'
// import axios from 'axios';

const props=defineProps({
    OrderLines:Object,
    session:Object
});


const form= useForm({

    item_no:'',
    vessel:'',
    from_vessel:'',
    to_vessel:'',
    qty:'',
    weight:'',
    packing_session_id:props.session.data.id,
    order_no:props.session.order_no,
})





const createOrUpdatesession=()=>{
    if (mode.state=='Create')
          form.post(route('packingSessionLine.store'),
                    {

                        onSuccess:()=>{
                                        form.reset();
                                        Swal.fire(`Line ${mode.state}ed Successfully!`,'','success');
                                      }
                    }
                   )
        else
     form.patch(route('packingSessionLine.update',form.session_no),
                {
                    onSuccess:()=>{ form.reset();
                                    Swal.fire(`Lines ${mode.state}ed Successfully!`,'','success');
                                }
                });
      showModal.value=false;


}


let mode= { state: 'Create' };



  let showModal=ref(false);


const showCreateModal=()=>{

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
    form.packing_session_id=prop.session.data.id

}
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
                                    <!-- <Button label="New" icon="pi pi-plus" class="mr-2" />
                                        <Button label="Upload" icon="pi pi-upload" class="p-button-success" /> -->
                                        <!-- <i class="mr-2 pi pi-bars p-toolbar-separator" /> -->
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="sessions" class="p-button-warning"></SplitButton> -->
                                    <Button
                                         label="Add"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>
                                <template #center>
                                    {{ session.data.order }}|
                                    {{ session.data.packer }}
                                </template>

                                    <template #end>
                                </template>
                                        </Toolbar>
                                        <div v-if="session.data.lines.length==0" class="mt-2 text-center p-3 w-full">
                                                 No Lines were found.
                                                </div>
                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg" v-else>

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
                                                           Description
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Order Qty
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Weight
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Vessel
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            From Vessel
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            To Vessel
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Actions
                                                        </th>



                                                    </tr>
                                                </thead>


                                                <tbody>
                                                    <tr v-for="line in sessions.data.lines" :key="session.id"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ line.item_no }}
                                                    </td>
                                                     <td class="px-3 py-2  flex flex-col items-center text-sm ">
                                                        {{ line.item_description }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ line.qty }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ line.weight}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ line.vessel }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ line.from_vessel}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ line.to_vessel}}
                                                    </td>
                                                     <td>
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('packingSessionLines.destroy',{'id':line.id})"/>
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text
                                                                        @click="showUpdateModal(line)"
                                                                      />
                                                       </div>
                                                    </td>

                                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <Toolbar>
                        <template #center>
                            <!-- <div >
                                <Pagination :links="sessions.meta.links" />
                            </div> -->
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
          v-model="form.item_no"
          :options="props.OrderLines.data"
          optionLabel="item_description"
          optionValue="item_no"
          filter=""
          placeholder="Select Item"
       />

       <Dropdown
          v-model="form.vessel"
          :options="['Crate','Carton']"

          placeholder="Select Vessel"
        />
        <InputText
          placeholder="Qty"
          v-model="form.qty"
        />
        <InputText
          placeholder="Weight"
          v-model="form.weight"
        />
        <InputText
          placeholder="From Vessel"
          v-model="form.from_vessel"
        />

        <InputText
          placeholder="To Vessel"
          v-model="form.to_vessel"
        />



        <Button
          severity="info"
          type="submit"
          :label=mode.state
          :disabled="form.item_no==''||form.vessel==''||form.qty==''||form.weight==''||form.from_vessel==''||form.to_vessel==''||form.processing"

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
