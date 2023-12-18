



<script setup>
import SearchBox from '@/Components/SearchBox.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import {useUserStore} from '@/Services/useUserStore'
import { ref,onMounted } from 'vue';


// const userStore = useUserStore();

// //  let showModal=ref(false);
// //  onMounted(() => {
// //   userStore.fetchData();
// //  });

//  const showCreateModal=()=>{
//     showModal.value=true;
//  }





</script>


<template>
    <Head title="Vessels"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
            <!-- <h2 class="text-xl font-semibold leading-tight text-gray-800">Vessels {{ vessels.data.length }}</h2> -->
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
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="vessels" class="p-button-warning"></SplitButton> -->
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
                                        <!-- <Pagination :links="vessels.meta.links" /> -->
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>


                                        <a :href="route('vessels.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="vessels"/>
                                        </a>




                                             <SearchBox model="vessels.index" />
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
                                                        #
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                          Type
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Order
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Route
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Packer
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                           Checker
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <!-- <div>
                                                        <button @click="userStore.fetchData">Fetch Data</button>
                                                        <div v-if="userStore.isLoading">Loading...</div>
                                                        <div v-else>
                                                        <div v-if="userStore.data">Data: {{ userStore.data }}</div>

                                                        </div>
                                                    </div> -->

<!--
                                                    <tr v-for="vessel in vessels.data" :key="vessel.vessel_no"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                    <td class="px-3 py-2 text-xs">
                                                        {{ vessel.id}}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ vessel.type }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ vessel.order }}
                                                    </td>

                                                    <td class="px-3 py-2 text-xs">

                                                            {{vessel.order.route}}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{vessel.packer}}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{vessel.checker}}

                                                    </td>
                                                    <td>
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('vessels.destroy',{'vessel':vessel.id}) "/>

                                                       </div>
                                                    </td>

                                            </tr> -->

                            </tbody>
                        </table>
                    </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <!-- <Pagination :links="vessels.meta.links" /> -->
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

       <VesselForm />

  </Modal>
</AuthenticatedLayout>

</template>
<style lang="scss" scoped>

</style>
