
<script setup>
import  {defineProps, ref, onMounted,watch } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  orders: Object,
  packers: Object,
  checkers: Object,
});

const form = useForm({
  vessel_type: '',
  order_no: '',
  packer_id: '',
  checker_id: '',
  lines: ref([]),
});

const addLine = () => {
  form.lines.push({ itemNumber: null, quantity: null, weight: null });
};

const removeLine = (index) => {
  form.lines.splice(index, 1);
};

// Declare `lines` as a ref
const lines = ref([]);

function filterOrdersByOrderNo(targetOrderNo) {
  const filteredOrders = props.orders.data.filter(order => order.order_no === targetOrderNo);

  if (filteredOrders.length === 0) {
    // Order not found
    return [];
  }

  // Return only the lines of the matched order
  return filteredOrders[0].lines;
}

const changeLines = (order_no) => {
  // Update the `lines` ref based on the selected order
  lines.value = filterOrdersByOrderNo(order_no);
};

// On component mount, initialize lines based on the first order






const refreshItem=(item_no) =>{
// console.log(item_no)
             let  filtered=lines.value.filter(obj => obj.item_no === item_no)
              if (filtered.length>0) {
                currentItem.value=filtered[0];
              }
    //   console.log(currentItem.value[0])
    }

let currentItem=ref({});

</script>

<template>
    <div class="w-full">



        <div class="w-full p-3 tracking-wide text-center text-black bg-slate-200">Order#{{ form.order_no }}</div>
        <div class="w-full p-3 tracking-wide text-center text-black bg-slate-200"></div>
        <hr/>
        <div class="w-full">
            <form class="p-3" @submit.prevent="submitForm()">
            <div>

                      <Dropdown
                    placeholder="Select Order"
                    :options="orders.data"
                    option-label="shp_name"
                    option-value="order_no"
                    v-model="form.order_no"
                    filter
                    @change="changeLines(form.order_no)"

                />
                   <Dropdown
                     placeholder="Vessel Type"
                     :options="['Crate','Carton']"
                     v-model="form.vessel_type"
                  />
                   <Dropdown
                    placeholder="Packer"
                    :options=packers
                    option-label="name"
                    option-value="id"
                    v-model="form.packer_id"
                  />
                  <Dropdown
                    placeholder="Checker"
                    :options=checkers
                    option-label="name"
                    option-value="id"
                    v-model="form.checker_id"
                  />

                </div>
                 <div v-for="(line, index) in form.lines" :key="index" class="space-x-3">
                    <Dropdown
                        placeholder="Select Item"
                        :options="lines"
                        option-label="item_description"
                        option-value="item_no"
                        v-model="line.itemNumber"
                        @change="refreshItem(line.itemNumber)"

                    />
                    <label>Order Qty{{currentItem.order_qty }}</label>
                   <label>Packed Qty</label>
                    <InputNumber v-model="line.quantity" type="number" placeholder="Quantity" />
                    <label>Weight</label>
                    <InputNumber v-model="line.weight" type="number" placeholder="Weight" />

                    <Button @click.prevent="removeLine(index)" icon="pi pi-minus" />
                </div>
                  <Button @click.prevent="addLine()" icon="pi pi-plus" />
                  <Button type="submit" icon="pi pi-send" severity="success" />

                </form>


        </div>


    </div>
</template>

<style lang="scss" scoped>

</style>
