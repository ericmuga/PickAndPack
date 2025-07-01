<script setup>
import { ref, computed } from 'vue';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
  batches: Array,
});

const selectedBatch = ref('');
const selectedPart = ref('');
const selectedShpDate = ref('');

// Unique shipment dates
const shipmentDates = computed(() => {
  const seen = new Set();
  return props.batches
    .filter(batch => {
      if (seen.has(batch.shp_date)) return false;
      seen.add(batch.shp_date);
      return true;
    })
    .map(batch => batch.shp_date)
    .sort();
});

// Unique batch numbers (filtered by selected ship date)
const batchOptions = computed(() => {
  const seen = new Set();
  return props.batches
    .filter(batch => {
      if (!selectedShpDate.value || batch.shp_date === selectedShpDate.value) {
        if (seen.has(batch.batch_number)) return false;
        seen.add(batch.batch_number);
        return true;
      }
      return false;
    })
    .map(batch => ({
      value: batch.batch_number,
      label: `${batch.batch_number} - ${batch.sp_code} | ${batch.sp_name}`,
    }));
});

// Auto-derive SP code and name
const selectedBatchInfo = computed(() => {
  return props.batches.find(b => b.batch_number === selectedBatch.value) || {};
});

const parts = computed(() => {
  return [...new Set(
    props.batches
      .filter(b => 
        b.batch_number === selectedBatch.value &&
        (!selectedShpDate.value || b.shp_date === selectedShpDate.value)
      )
      .map(b => b.part)
  )];
});

// Grouped items
const groupedItems = computed(() => {
  const filtered = props.batches.filter(row =>
    row.batch_number === selectedBatch.value &&
    row.part === selectedPart.value &&
    (!selectedShpDate.value || row.shp_date === selectedShpDate.value)
  );

  const grouped = {};
  for (const row of filtered) {
    const key = row.item_no;
    if (!grouped[key]) {
      grouped[key] = { ...row, total_qty: 0 };
    }
    grouped[key].total_qty += row.total_qty;
  }

  return Object.values(grouped);
});

const toFloat = (value) => {
  if (value === null || value === undefined) return 0;
  const parsedValue = parseFloat(String(value).replace(/,/g, ''));
  return isNaN(parsedValue) ? 0 : parseFloat(parsedValue.toFixed(2));
};

const exportToPDF = () => {
  const doc = new jsPDF();
  doc.text(`Pick# ${selectedBatch.value} - Part ${selectedPart.value}`, 14, 10);
  doc.text(`SP Code: ${selectedBatchInfo.value.sp_code} - ${selectedBatchInfo.value.sp_name}`, 14, 18);
  doc.text(`Shipment Date: ${selectedShpDate.value || 'All'}`, 14, 26);
  // Insert a blank line for spacing in the PDF
  // doc.text(' ', 14, 34);
  autoTable(doc, {
    startY: 28,
    head: [['Item No', 'Description', 'Quantity']],
    body: groupedItems.value.map(row => [
      row.item_no,
      row.item_description,
      toFloat(row.total_qty),
    ]),
  });

  doc.save(`Batch_${selectedBatch.value}_Part_${selectedPart.value}.pdf`);
};
</script>

<template>
    <AuthenticatedLayout>
  <div class="p-4">
    <h1 class="mb-4 text-2xl font-bold">Consolidated Picks</h1>

    <!-- Select Form -->
    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-4">
      <div>
        <label class="block text-sm font-medium">Shipment Date</label>
        <select v-model="selectedShpDate" class="w-full px-2 py-1 border rounded">
          <option value="" disabled>Select Date</option>
          <option
        v-for="date in shipmentDates"
        :key="date"
        :value="date"
          >
        {{ date }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Consolidated Pick</label>
        <select v-model="selectedBatch" class="w-full px-2 py-1 border rounded">
          <option value="" disabled>Select Batch</option>
          <option v-for="option in batchOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Part</label>
        <select v-model="selectedPart" class="w-full px-2 py-1 border rounded">
          <option value="" disabled>Select Part</option>
          <option v-for="part in parts" :key="part" :value="part">{{ part }}</option>
        </select>
      </div>

      <div class="self-end">
        <button
          @click="exportToPDF"
          :disabled="!selectedBatch || !selectedPart"
          class="w-full px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
        >
          Export to PDF
        </button>
      </div>
    </div>

    <!-- Grouped Table -->
    <table class="w-full text-sm border table-auto" v-if="groupedItems.length">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-2 py-1 border">Shipment Date</th>
          <th class="px-2 py-1 border">Item No</th>
          <th class="px-2 py-1 border">Description</th>
          <th class="px-2 py-1 border">Total Qty</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in groupedItems" :key="row.item_no">
          <td class="px-2 py-1 border">{{ row.shp_date }}</td>
          <td class="px-2 py-1 border">{{ row.item_no }}</td>
          <td class="px-2 py-1 border">{{ row.item_description }}</td>
          <td class="px-2 py-1 border">{{ toFloat(row.total_qty) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</AuthenticatedLayout>
</template>
