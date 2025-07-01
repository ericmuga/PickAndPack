<script setup>
import { ref, onMounted } from 'vue'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import MultiSelect from 'primevue/multiselect'
import Button from 'primevue/button'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  orders: Array,
})

const toast = useToast()
const isLoading = ref(false)
const page = usePage()

// Show flash messages
onMounted(() => {
  if (page.props.value.flash?.success) {
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: page.props.value.flash.success,
      life: 5000
    })
  }
  if (page.props.value.flash?.error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: page.props.value.flash.error,
      life: 5000
    })
  }
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

const partOptions = 'A, B, C, D'.split(', ').map(part => ({
  label: part,
  value: part,
}))

const selectedSPCodes = ref([])
const selectedPart = ref('')
const selectedShipDate = ref('')

const form = useForm({
  sp_codes: [],
  shp_date: '',
  selected_part: '',
})

const createBatch = async () => {
  if (selectedSPCodes.value.length === 0) {
    toast.add({
      severity: 'warn',
      summary: 'Warning',
      detail: 'Please select at least one SP Code',
      life: 3000
    })
    return
  }

  if (selectedPart.value.length === 0) {
    toast.add({
      severity: 'warn',
      summary: 'Warning',
      detail: 'Please select at least one Part',
      life: 3000
    })
    return
  }

  isLoading.value = true
  
  form.sp_codes = selectedSPCodes.value
  form.selected_part = selectedPart.value
  form.shp_date = form.shp_date
  
  console.log('Creating batch with:', form)
  
  form.post('/orders/create-batch', {
    onSuccess: () => {
      // Reset form
      selectedSPCodes.value = []
      selectedPart.value = ''
      form.reset()
    },
    onError: (errors) => {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Failed to create batch. Please check your inputs and try again.',
        life: 5000
      })
      console.error('Batch creation failed:', errors)
    },
    onFinish: () => {
      isLoading.value = false
    }
  })
}
  
</script>

<template>
  <div class="p-6">
    <AuthenticatedLayout>
      <Toast />
      
      <h1 class="mb-4 text-2xl font-bold">Unbatched Orders</h1>

      <!-- Ship Date Filter -->
      <div class="w-full mb-6 md:w-1/2">
        <label class="block mb-2 font-semibold">Filter by Ship Date:</label>
        <!-- <Calendar v-model="form.shp_date" dateFormat="yy-mm-dd" showIcon class="w-full" /> -->
         <input type="date" v-model="form.shp_date" class="w-full p-2 border rounded" placeholder="YYYY-MM-DD" />
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
          :disabled="isLoading"
        />
      </div>

      <div class="w-full mb-6 md:w-1/2">
        <label class="block mb-2 font-semibold">Select Part:</label>
        <MultiSelect
          v-model="selectedPart"
          :options="partOptions"
          optionLabel="label"
          optionValue="value"
          placeholder="Choose Part"
          display="chip"
          class="w-full"
          :disabled="isLoading"
        />
      </div>

      <Button
        :label="isLoading ? 'Creating Batch...' : 'Create Consolidated Pick'"
        :icon="isLoading ? 'pi pi-spin pi-spinner' : 'pi pi-check'"
        class="px-4 py-2 mb-8 text-white bg-blue-600 rounded"
        :class="{ 'opacity-75 cursor-not-allowed': isLoading }"
        :disabled="isLoading"
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
