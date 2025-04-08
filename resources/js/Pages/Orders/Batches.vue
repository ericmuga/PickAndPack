<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
  batches: Array,
})
</script>
<template>
  <div class="p-4">
    <h1 class="mb-4 text-2xl font-bold">Batch Summary (Per Item)</h1>

    <table class="w-full text-sm border table-auto">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-2 py-1 border">Batch</th>
          <th class="px-2 py-1 border">SP Code</th>
          <th class="px-2 py-1 border">SP Name</th>
          <th class="px-2 py-1 border">Ship Date</th>
          <th class="px-2 py-1 border">Item No</th>
          <th class="px-2 py-1 border">Description</th>
          <th class="px-2 py-1 border">Part</th>
          <th class="px-2 py-1 border">Qty</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in batches" :key="`${row.batch_number}-${row.item_no}-${row.part}`">
          <td class="px-2 py-1 border">{{ row.batch_number }}</td>
          <td class="px-2 py-1 border">{{ row.sp_code }}</td>
          <td class="px-2 py-1 border">{{ row.sp_name }}</td>
          <td class="px-2 py-1 border">{{ row.shp_date }}</td>
          <td class="px-2 py-1 border">{{ row.item_no }}</td>
          <td class="px-2 py-1 border">{{ row.item_description }}</td>
          <td class="px-2 py-1 border">{{ row.part }}</td>
          <td class="px-2 py-1 border">{{ row.total_qty }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>


