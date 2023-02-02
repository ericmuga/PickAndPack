<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';
import { useForm } from '@inertiajs/inertia-vue3'

const form = useForm({
    searchKey: null,

})

const search=()=>form.post('/getOrder', {preserveScroll: true});

</script>

<template>
    <Head title="Orders"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Orders</h2>
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
                                        <!-- <i class="pi pi-bars p-toolbar-separator mr-2" /> -->
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="items" class="p-button-warning"></SplitButton> -->
                                    </template>

                                    <template #end>
                                        <!-- <h5>Advanced with Templating and Filtering</h5> -->
                                        <form @submit.prevent="search()">
                                            <InputText v-model="searchKey" aria-placeholder="search" :disabled="form.processing"/>
                                            <Button icon="pi pi-search" class="mr-2" type="submit"/>
                                        </form>



                                        <!-- <Button icon="pi pi-calendar" class="p-button-success mr-2" />
                                            <Button icon="pi pi-times" class="p-button-danger" /> -->
                                        </template>
                                    </Toolbar>

                                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3">
                                                        Order#
                                                    </th>
                                                    <th scope="col" class="px-6 py-3">
                                                        Item
                                                    </th>
                                                    <th scope="col" class="px-6 py-3">
                                                        Qty
                                                    </th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                                    <th scope="row" class="px-6 py-4 font-small text-xs text-gray-900 whitespace-nowrap dark:text-white">
                                                        S+SRD001456
                                                    </th>
                                                    <td class="px-6 py-4 text-xs">
                                                        J301010101-Pork Chipolata
                                                    </td>
                                                    <td class="px-6 py-4 text-xs">
                                                        60
                                                    </td>

                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- <div class="col-span-4">
                                        <Pagination :links=members.links :prefix="`members`" />
                                    </div>

                                    <div class="w-full text-center">
                                        <div v-if="members.data.length===0">
                                            No members were found
                                        </div>
                                    </div> -->


                                </div>




                                <!--end of stats bar-->

                            </div>
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        </template>
