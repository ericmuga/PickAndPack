

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm,Link} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { ref, reactive } from 'vue';
import AssignmentCard from "@/Components/AssignmentCard.vue";
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'

    const props = defineProps({
                        assignments   : Object,
           })

</script>

<template>
 <Head title="Assignments"/>

    <AuthenticatedLayout>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Assignments </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>
                                    <Link :href="route('assignment.create')">
                                    <Button
                                         label="Add"
                                         icon="pi pi-plus"
                                         severity="success"

                                         rounded
                                    ></Button> </Link>
                                </template>
                                <template #center>
                                    <div>
                                        <!-- <Pagination :links="assignments.meta.links" /> -->
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>


                                        <!-- <a :href="route('items.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="Items"/>
                                        </a> -->




                                             <SearchBox model="assignments.index" />
                                    </template>
                                        </Toolbar>





 <div class="grid sm:grid-cols-1 md:grid-cols-4">

       <div v-if="assignments.data.length==0" class="p-5 text-center text-teal-500" > No assignments were found</div>

       <div v-for="assignment in assignments.data" :key="assignment.id">
          <AssignmentCard :assignment="assignment"  />
       </div>
</div>
                                      </div>
                                      </div>
                                      </div>
                                      </div>
    </div>
    </AuthenticatedLayout>
</template>



<style lang="scss" scoped>

</style>
