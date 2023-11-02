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
'name':''
})





const createOrUpdateRole=()=>{
    if (mode.state=='Create')
          form.post(route('roles.store'),
                    { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`Role ${mode.state}d Successfully!`,'','success');
                                    }
                    }
                   )
        else
     form.patch(route('roles.update',form.plate),
                { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`Role ${mode.state}d Successfully!`,'','success');
                                    }
                    })
      showModal.value=false;


}


let mode= { state: 'Create' };

const props=  defineProps({
       roles:Object
  })

  let showModal=ref(false);


const showCreateModal=()=>{
    form.reset();
    mode.state='Create'

    showModal.value=true

}

const showUpdateModal=(role)=>{
// alert('here');
    mode.state='Update'
    form.name=role.name;

    showModal.value=true;
}
</script>


<template>
    <Head title="Roles"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Role </h2>
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
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="Roles" class="p-button-warning"></SplitButton> -->
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
                                        <Pagination :links="roles.meta.links" />
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>


                                        <a :href="route('roles.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="Roles"/>
                                        </a>




                                             <SearchBox :model="roles.index" />
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
                                                           Role Name
                                                        </th>




                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="role in roles.data" :key="role.id"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ role.name}}
                                                    </td>


                                                    <td>
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('roles.destroy',{'role':role.id})"/>
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text
                                                                      @click="showUpdateModal(role)"
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
                                <Pagination :links="roles.meta.links" />
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

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Role</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update Role</div> -->

          <form  @submit.prevent="createOrUpdateRole()">

<div class="flex flex-col justify-center gap-3">


        <InputText
           placeholder="Role Name"

           v-model="form.name"
        />





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
