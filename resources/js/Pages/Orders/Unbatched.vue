<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import MultiSelect from 'primevue/multiselect'
import Button from 'primevue/button'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  orders: Array,
})


// Create unique SP Code + Name options
const spOptions = ref(
  [...new Map(
    props.orders.map(o => [`${o.sp_code}|${o.sp_name}`, o])
  ).values()].map(({ sp_code, sp_name }) => ({
    label: `${sp_code} - ${sp_name}`,
    value: sp_code,
  }))
)

const selectedSPCodes = ref([])

const form = useForm({
  sp_codes: [],
    shp_date: '',
})

const createBatch = () => {
  form.sp_codes = selectedSPCodes.value
  form.post('/orders/create-batch')
}
</script>



<template>
  <div class="p-6">
    <AuthenticatedLayout>
      <h1 class="mb-4 text-2xl font-bold">Unbatched Orders</h1>

      <!-- Ship Date Filter -->
      <div class="w-full mb-6 md:w-1/2">
        <label class="block mb-2 font-semibold">Filter by Ship Date:</label>
        <Calendar v-model="form.shpDate" dateFormat="yy-mm-dd" showIcon class="w-full" />
      </div>

      <!-- SP Code Selection -->
      <div class="w-full mb-6 md:w-1/2">
        <label class="block mb-2 font-semibold">Select SP Codes:</label>
        <MultiSelect
          v-model="selectedSPCodes"
          :options="spOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="Choose SP Codes"
          display="chip"
          class="w-full"
        />
      </div>

      <Button
        label="Create Consolidated Pick"
        icon="pi pi-check"
        class="px-4 py-2 mb-8 text-white bg-blue-600 rounded"
        @click="createBatch"
      />

      <!-- Unbatched Orders Table -->
      <h2 class="mb-3 text-xl font-semibold">Unbatched Orders List</h2>
      <table class="w-full text-sm border border-collapse border-gray-300 table-auto">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-2 py-1 text-left border">Order No</th>
            <th class="px-2 py-1 text-left border">SP Code</th>
            <th class="px-2 py-1 text-left border">SP Name</th>
            <th class="px-2 py-1 text-left border">Customer</th>
            <th class="px-2 py-1 text-left border">Ship Date</th>
            <th class="px-2 py-1 text-left border">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
            <td class="px-2 py-1 border">{{ order.order_no }}</td>
            <td class="px-2 py-1 border">{{ order.sp_code }}</td>
            <td class="px-2 py-1 border">{{ order.sp_name }}</td>
            <td class="px-2 py-1 border">{{ order.customer_name }}</td>
            <td class="px-2 py-1 border">{{ order.shp_date }}</td>
            <td class="px-2 py-1 border">{{ order.status }}</td>
          </tr>
        </tbody>
      </table>
    </AuthenticatedLayout>
  </div>
</template>
