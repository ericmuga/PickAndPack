



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
    name:'',
    email:'',
    // pass:'',
    roles:[]
})





const createOrUpdateUser=()=>{
    if (mode.state=='Create')
          form.post(route('users.store'),
                    { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`User ${mode.state}d Successfully!`,'','success');
                                    }
                    }
                   )
        else
     form.patch(route('users.update',form.email),
                { preserveScroll: true,
                      onSuccess: () =>{ form.reset()
                                      Swal.fire(`User ${mode.state}d Successfully!`,'','success');
                                    }
                    })
      showModal.value=false;


}


let mode= { state: 'Create' };

const props=  defineProps({
       users:Object,
       roles:Object,
       permissions:Object,
  })

  let showModal=ref(false);


const showCreateModal=()=>{
    form.reset();
    mode.state='Create'

    showModal.value=true

}

const showUpdateModal=(user)=>{

    mode.state='Update'
    form.name=user.user_name
    form.email=user.email
    form.pass=user.pass
    form.roles=user.roles

    showModal.value=true
}
</script>


<template>
    <Head title="Users"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">User {{ users.data.length }}</h2>
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
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="users" class="p-button-warning"></SplitButton> -->
                                    <Button
                                         label="Add"
                                         icon="pi pi-plus"
                                         disabled
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>
                                <template #center>
                                    <div>
                                        <Pagination :links="users.meta.links" />
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>


                                        <a :href="route('users.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="users"/>
                                        </a>




                                             <SearchBox  :model="route('users.index')" />
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
                                                           Name
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            email
                                                        </th>
                                                         <th scope="col" class="px-6 py-3 text-center">
                                                            roles
                                                        </th>


                                                        <!-- <th scope="col" class="px-6 py-3">

                                                        </th>
                                                        <th scope="col" class="px-6 py-3">

                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                           Actions
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="user in users.data" :key="user.user_no"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ user.user_name }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ user.email }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        <div v-for="item in user.roles" :key="item.id">
                                                          {{ item.name }}
                                                        </div>
                                                    </td>
                                                    <!-- <td class="px-3 py-2 text-xs font-bold">
                                                        {{ user.description }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs">

                                                            {{user.prepacks.length}}

                                                    </td> -->
                                                    <td>
                                                       <div class="flex flex-row">
                                                          <!-- <Drop  :drop-route="route('users.destroy',{'user':user.id})"/> -->
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text


                                                                      @click="showUpdateModal(user)"
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
                                <Pagination :links="users.meta.links" />
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

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} User</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update user</div> -->

          <form  @submit.prevent="createOrUpdateUser()">

<div class="flex flex-col justify-center gap-3">


        <InputText
           placeholder="Name"

           v-model="form.name"
        />


        <InputText
           disabled="true"
           placeholder="email"
           v-model="form.email"

        />

        <table>
            <!-- <tr v-for="role in props.user.roles"></tr> -->
        </table>

        <!-- <Password v-model="form.Password" :feedback="true" /> -->

        <MultiSelect
         v-model="form.roles"
         :options="roles.data"
         optionValue="name"
         optionLabel="name"
         placeholder="Role"
         filter

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
