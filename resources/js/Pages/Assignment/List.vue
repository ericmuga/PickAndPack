

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
import ProgressBar from 'primevue/progressbar';


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
                                    // console.log(response.data)
                                    for (let index = 0; index < response.data.lines.length; index++) {
                                        orders+='<tr><td class="p-2 text-left">'+response.data.lines[index].order_no+'</td><td class="p-2 text-center">'+response.data.lines[index].part+'</td><td class="p-2 text-left">'+response.data.lines[index].order.shp_name+'</td></tr>';

                                    }
                                            Swal.fire({
                                                    title: 'Contents',
                                                    html: `
                                                        <div id="pdf-modal" class="p-4 bg-orange-100 rounded-lg">
                                                           <p class="font-bold text-center text-black bg-slate-300"> Weight: ${response.data.lines_weight} Kg </p>
                                                           <p class="font-bold text-center text-black bg-slate-300"> Items: ${response.data.lines_count} </p>
                                                           <p class="font-bold text-center text-black bg-slate-300"> Orders: ${response.data.orders_count} </p>
                                                             <table class="p-1 text-sm font-semibold " >  ${orders}</table>


                                                           <p> Assigned ${response.data.time} </p>
                                                           <p> Assembly Time: ${response.data.total_time} </p>
                                                        </div>`,
                                                    showConfirmButton: false,
                                                    });
                                                    // console.log(response.data);
                                        })
                                // .catch(response) => {
                                //     // console.log(response)
                                // Swal.fire('Error', response.data.message, 'error');
                                //     });

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
                                    <!-- <Link :href="route('assignment.create')">
                                        <Button
                                            label="Add"
                                            icon="pi pi-plus"
                                            severity="success"

                                            rounded
                                        >
                                        </Button>
                                    </Link> -->

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

                          <SearchBox :model="route('assignment.index')" />
                                    </template>
                                        </Toolbar>





 <!-- <div class="grid sm:grid-cols-1 md:grid-cols-4"> -->

       <div v-if="assignments.data.length==0" class="p-5 text-center text-teal-500" > No assignments were found</div>
 <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
     <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-slate-300">
                                                        <th scope="col" class="px-2 py-1">
                                                            #
                                                        </th>

                                                        <th scope="col" class="px-2 py-1">
                                                            Order No.
                                                        </th>
                                                        <th scope="col" class="px-2 py-1">
                                                            Ship-to
                                                        </th>
                                                         <th scope="col" class="px-2 py-1">
                                                            Route
                                                        </th>
                                                        <th scope="col" class="px-2 py-1">
                                                            Assignee
                                                        </th>
                                                        <th scope="col" class="px-2 py-1">
                                                           Assignor
                                                        </th>
                                                        <th scope="col" class="px-2 py-1 text-center">
                                                            Assignment Time
                                                        </th>
                                                        <th scope="col" class="px-2 py-1 text-center">
                                                            Total Time
                                                        </th>

                                                        <th scope="col" class="px-2 py-1">
                                                            Orders Count
                                                        </th>
                                                        <th scope="col" class="px-2 py-1">
                                                            Lines Count
                                                        </th>

                                                         <th scope="col" class="px-2 py-1">
                                                            Percentage
                                                        </th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     <tr  v-for="assignment in assignments.data" :key="assignment.id"
                                                      class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:bg-slate-100 hover:text-black"
                                                      @click="showContents(assignment.id)"

                                                      >

                                                    <td class="px-3 py-2 text-xs">
                                                        {{assignment.id }}
                                                    </td>

                                                     <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ assignment.lines[0].order.order_no }}
                                                    </td>
                                                     <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ assignment.lines[0].order.shp_name }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ assignment.lines[0].order.sp_code+'|'+assignment.lines[0].order.sp_name }}
                                                    </td>
                                                     <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ assignment.assignee }}
                                                    </td>

                                                     <td class="px-3 py-2 text-xs">

                                                            {{ assignment.assignor }}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{ assignment.time }}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{ assignment.total_time }}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{ assignment.orders_count }}

                                                    </td>
                                                    <td class="px-3 py-2 text-xs">

                                                            {{ assignment.lines_count }}

                                                    </td>
                                                    <td class="items-center p-3 m-2 text-center text-white" :class="(assignment.percentage==100)? 'bg-lime-800 rounded-full':'bg-rose-900   rounded-full'" >
                                                        {{assignment.percentage}} {{ (assignment.percentage==100)?'Complete!':'In Progress...' }}
                                                    </td>

                                                  </tr>

                                                </tbody>
 </table>
<!-- </div>
       <div v-for="assignment in assignments.data" :key="assignment.id">
          <AssignmentCard :assignment="assignment" @click="showContents(assignment.id)" class="shadow-md hover:shadow-lg hover:cursor-pointer hover:shadow-orange-400" />
       </div>

</div> -->

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
    </div>
    </AuthenticatedLayout>
</template>



<style lang="scss" scoped>

</style>
