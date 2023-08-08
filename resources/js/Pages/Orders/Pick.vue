<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';


import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref,onMounted} from 'vue';

import Swal from 'sweetalert2'
import Pagination from '@/Components/Pagination.vue';
import SearchBox from '@/Components/SearchBox.vue'

const props=defineProps({
                            picks:Object,
                            previous:String,
                            lines:Object,
                        })

const inputField=ref(null);
onMounted(() => {
    inputField.value.focus();
});

let newItem=ref('');
watch( newItem,
debounce( ()=>{ if (newItem.value!='')
                   Inertia.get(route('order.pick'),{'search':newItem.value})
                else{
                     // if the value is empty
                }



              }
        )
,500);

</script>


<template>
    <Head title="Pick List"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Assemble Pick List</h2>
        </template>

        <div class="py-6">
            <!-- <Modal :show="true" > Hi there </Modal> -->
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->

                        <div>
                            <Toolbar>
                                <template #start>

                                </template>
                                <template #center>
                                    <div>

                                        <input type="text" v-model="newItem"  ref="inputField"
                                         placeholder="Scan Your Pick"
                                        class="m-2 rounded-lg bg-slate-300 text-md">
                                    <div v-if="picks.data.length==0" class="text-center">
                                        No Picks were found
                                    </div>
                                    </div>


                                </template>

                                    <template #end>




                                            <!-- <InputText v-model="search" aria-placeholder="search"/> -->


                                            </template>
                                        </Toolbar>

                                        <div class="grid grid-cols-3 gap-2">
                                            <div class="col-span-1 p-3 mx-1 border-green-100 border-1">
                                               <div class="text-center">
                                                <Pagination :links="picks.links" />
                                                 <span>{{ picks.data }}</span>
                                               </div>

                                                 Pick details and scan and a field to scan the item
                                            </div>
                                            <div class="relative col-span-2 overflow-x-auto shadow-md sm:rounded-lg">




                                             </div>
                                        </div>








                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>
</AuthenticatedLayout>
</template>
