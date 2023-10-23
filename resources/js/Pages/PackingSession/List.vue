<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,useForm} from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Pagination from '@/Components/Pagination.vue';

const props=defineProps({
    sessions:Object,
    packersList:Object,
    date:String,
})

 const form = useForm({
        packers:props.packers,
        date:props.date,
        rows:'5'
    })




</script>
<template>
    <div>

        <Head title="Packing Sessions"/>

    <AuthenticatedLayout>

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Packing Sessions </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>


                                </template>
                                <template #center>

                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>



                                 <form @submit.prevent="form.get(route('packingSession.index'))">
                                    <span class="mx-3">Rows</span>
                                    <Dropdown


                                          v-model="form.rows"
                                          :options="['10','20','50','100','200','1000']"
                                         />

                                    <MultiSelect


                                          v-model="form.packers"
                                          :options="packersList"
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

                          <SearchBox model="packingSession.index" />
                                    </template>
                                        </Toolbar>





 <div class="">
 <div  class="relative overflow-x-auto shadow-md sm:rounded-lg">
       <div v-if="sessions.data.length==0" class="p-5 text-center text-teal-500" > No sessions were found</div>



<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

        <tr class="text-white bg-gray-700 ">
            <!-- <th scope="col" class="px-2 py-2">
                Barcode
            </th> -->
            <th scope="col" class="px-2 py-2">
                Order No.
            </th>
            <th scope="col" class="px-2 py-2 text-center">
                Sales Person
            </th>
            <th scope="col" class="px-2 py-2">
                Ship-to Name
            </th>
            <th scope="col" class="px-2 py-2">
                Shipment Date
            </th>


            <th scope="col" class="px-6 py-3 text-center">
                Part
            </th>

            <th scope="col" class="px-6 py-3 text-center">
                Packer
            </th>

            <th scope="col" class="px-6 py-3 text-center">
                Packing Time
            </th>

            <th scope="col" class="px-6 py-3 text-center">
                Packed at
            </th>



        </tr>
    </thead>
    <tbody>
        <tr v-for="session in sessions.data" :key="session.id"
        class="font-semibold text-black bg-white hover:bg-gray-300">

        <td class="px-2 py-2 text-xs break-all">
            {{ session.order.order_no }}
        </td>
        <td class="flex flex-col px-2 py-2 text-xs text-center">
            <span class="text-xs font-bold">{{session.order.sp_code}}</span>
            <span class="text-xs font-thin">{{session.order.sp_name}}</span>
        </td>
        <td class="px-2 py-2 text-xs font-bold text-center capitalize bg-yellow-200 rounded-full">
            {{ session.order.shp_name }}
        </td>
        <td class="px-2 py-2 text-xs font-bold">
            {{ session.order.shp_date }}
        </td>
         <td class="items-center px-2 py-2 text-xs font-bold text-center">
            {{ session.part }}
        </td>
         <td class="px-2 py-2 text-xs font-bold">
            {{ session.user.user_name }}
        </td>
         <td class="px-2 py-2 text-xs font-bold text-blue-500">
            {{ session.packing_time }}
        </td>
         <td class="px-2 py-2 text-xs font-bold text-blue-500">
            {{ session.packed_at }}
        </td>




</tr>

</tbody>
</table>



                                        </div>





</div>
                                      </div>
                                      <div class="w-full text-center">
                                        <Pagination :links="sessions.meta.links" />
                                    </div>
                                      </div>
                                      </div>
                                      </div>

    </div>
    </AuthenticatedLayout>

    </div>
</template>



<style lang="scss" scoped>

</style>
