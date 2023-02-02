<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';


}
</script>

<template>
    <Head title="Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Orders</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->
                        <div class=" md:grid md:grid-cols-3 md:space-x-1 w-full items-center justify-between md:gap-1  sm:space-y-2 md:flex-row">

                          <Toolbar/>



                         </div>
                        <!--end of stats bar-->

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
