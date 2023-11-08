

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm,Link} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { onMounted,ref } from 'vue';
import AssignmentCard from "@/Components/AssignmentCard.vue";
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import { stringify } from 'postcss';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';



    const props = defineProps({
                        assignments   : Object,
                        assemblers:Object,
                        dateParam:String,
                        assemblersParam:[],
           })

    const form = useForm({
        assemblers:props.assemblersParam,
        date:props.dateParam
    })

    let content=ref('');

    const showContents = (id) => {

                        axios.get(route('assignment.show',id))
                            .then((response)=>{

                                    let orders='';
                                    console.log(response.data.lines)
                                    for (let index = 0; index < response.data.lines.length; index++) {
                                        orders+='<p>'+response.data.lines[index].order_no+'-'+response.data.lines[index].part+'-'+response.data.lines[index].order.shp_name+'</p>';

                                    }
                                            Swal.fire({
                                                    title: 'Contents',
                                                    html: `
                                                        <div id="pdf-modal">
                                                           <p> Weight: ${response.data.lines_weight} Kg </p>

                                                           <p> Orders: ${response.data.orders_count} </p>
                                                               ${orders}

                                                           <p> Items: ${response.data.lines_count} </p>
                                                           <p> Assembly Time: ${response.data.total_time} </p>
                                                           <p> Assembly Time: ${response.data.total_time} </p>
                                                        </div>`,
                                                    showConfirmButton: false,
                                                    });
                                                    // console.log(response.data);
                                        })
                                .catch((error) => {
                                    // console.log(response)
                                // Swal.fire('Error', response.data.message, 'error');
                                    });

                };



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
                                        >
                                        </Button>
                                    </Link>

                                </template>
                                <template #center>
                                    <div>
                                        <Pagination :links="assignments.meta.links" />
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>



                                 <form @submit.prevent="form.get(route('assignment.index'))">


                                    <MultiSelect


                                          v-model="form.assemblers"
                                          :options="assemblers"
                                          optionLabel="name"
                                          optionValue="id"
                                          filter
                                        />

                                         <input
                                           type="date"
                                           class="p-3 mx-2 rounded-md"
                                           v-model="form.date"

                                         />

                                        <Button
                                          severity="success"
                                          icon="pi pi-search"
                                          label="Go!"
                                          type="submit"
                                          :disabled="form.processing"
                                          class="mx-2"
                                        />



                                    </form>

                          <SearchBox model="assignments.index" />
                                    </template>
                                        </Toolbar>





 <div class="grid sm:grid-cols-1 md:grid-cols-4">

       <div v-if="assignments.data.length==0" class="p-5 text-center text-teal-500" > No assignments were found</div>

       <div v-for="assignment in assignments.data" :key="assignment.id">
          <AssignmentCard :assignment="assignment" @click="showContents(assignment.id)" />
       </div>

</div>
                                      </div>
                                      </div>
                                      </div>
         <Toolbar>
             <template #center>

                <Pagination :links="assignments.meta.links" />
             </template>


       </Toolbar>
                                      </div>
    </div>
    </AuthenticatedLayout>
</template>



<style lang="scss" scoped>

</style>
