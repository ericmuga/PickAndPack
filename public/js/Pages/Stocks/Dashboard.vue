<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import StatsTile from '@/Components/StatsTile.vue';
import { Link } from '@inertiajs/inertia-vue3'
import SpacedRule from '@/Components/SpacedRule.vue';
import DataTable from '@/Components/DataTable.vue';
import PieChart from '@/Components/PieChart.vue';
import {ref} from 'vue'
import Calendar from 'primevue/calendar';
import ProgressBar from 'primevue/progressbar';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/inertia-vue3'
defineProps({
               todays:Number,
               pending:Number,
               refreshError:String,
               stocks:Object,
               headers:Object,
               top5Labels:Object,
               top5Weights:Object,
               items:Object,
               chillers:Object,
          })

const form=useForm({
   item_no:'',
   stock_date:new Date(),
   pieces:'',
   weight:'',
   chiller_code:'',
   location:'3535'
});

let showModal=ref(false);
let closeModal=ref(true);

const cdata = ref({
                    labels: ['Red', 'Blue', 'Yellow'],
                    datasets: [
                        {
                        label: 'My Dataset',
                        data: [10, 20, 30],
                        backgroundColor: ['red', 'blue', 'yellow'],
                        },
                    ],
                });
const submitForm=()=>{

    form.post(route('stocks.store'))
    form.reset()
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout @add="showModal=true">
        <!-- <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template> -->

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!--stats bar -->
                        <div v-if="refreshError!=null">
                            {{ refreshError }}
                        </div>
                        <div class="items-center justify-between w-full md:grid md:grid-cols-3 md:space-x-1 md:gap-1 sm:space-y-2 md:flex-row">

                            <!-- <Link :href="route('order.list')" :active="route().current('order.list')">

                               <StatsTile :Qty=todays tile="Todays" class="text-black bg-cyan-100" />
                            </Link>
                            <StatsTile tile="Confirmed" :Qty=todays-pending  class="text-white bg-teal-600 " />
                            <StatsTile :Qty=pending tile="Pending"  class="text-white bg-rose-700 md:mt-2" /> -->


                                <div class="card">
                                    <!-- <span class="text-xs">Pending Confirmation {{ pending}}/{{ todays }}</span>
                                <div class="card">
                                <ProgressBar :value="pending"

                                            >{{ pending}}/{{ todays }} </ProgressBar>
                                        </div>-->

                                  <Button

                                    label=" Stock Take"
                                    severity="warning"
                                    @click="showModal=true"
                                    />

                                </div>
                                <!-- <Link :href="route('refresh')" class="w-5 h-10 m-10 mx-auto text-center ">
                                        <span class="text-xs">Refresh</span>
                                       <img src="/img/refresh.png" />
                                       <Button icon="pi pi-refresh" severity="warning" aria-label="Filter" />

                                    </Link> -->





                         </div>

                        <!--end of stats bar-->

                    </div>

                        <div class="flex items-center w-full text-center">
                                     <SpacedRule class="text-center "/>

                                      <!-- <Link :href="route('scanner')" class="w-20 h-20 m-5 mx-auto text-center ">
                                        <img src="/img/scan.png" />
                                        <img src="/img/scanner.jpg" />
                                    </Link> -->




                        </div>

                        <div class="grid grid-cols-1 ">

                            <div class="col-span-2 mx-2 my-2">
                                <DataTable
                                  class="text-xs"
                                  :searchUrl="route('stocks.index')"
                                  :items="stocks"
                                  :headers="headers"

                                />

                            </div>

                            <!-- <div class="col-span-1">

                                <PieChart
                                :top5Labels="top5Labels"
                                :top5Weights="top5Weights"

                                />
                            </div> -->

                        </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <div>
        <Modal :show="showModal" @close="showModal=false" :errors="errors"> <!-- {{ dynamicModalContent  }} -->
     <!-- {{ showModal }} -->

     <div class="p-4 font-bold text-center text-white bg-slate-600"> Stock Take</div>
       <div>


        <form @submit.prevent="submitForm()" class="flex flex-col justify-center gap-2 p-5">

        <Calendar v-model="form.stock_date"  showIcon />

        <!-- <div class="flex card justify-content-center"> -->
           <Dropdown v-model="form.item_no"
              :options="items"
              optionLabel="description"
              optionValue="item_no"
              placeholder="Select an Item"
              class="w-full md:w-14rem"
              filter
            />
       <!-- </div> -->


           <InputText
             ref="scanItem"
             v-model="form.pieces"
             placeholder="Pieces"
           />

           <InputText
             v-model="form.weight"
             placeholder="Weight"
           />


           <Dropdown v-model="form.chiller_code"
              :options="chillers"
              optionLabel="chiller_code"
              optionValue="chiller_code"
              placeholder="Select a chiller"
              class="w-full md:w-14rem"
              filter
            />

            <!-- <input v-model="form.shp_date" placeholder="Shipment Date" type="date"/> -->
            <!-- {{currentItem}} -->
            <Button  label="Save" icon="pi pi-send" class="w-sm" severity="success"  type="submit" :disabled="form.processing" />
            <Button label="Cancel" severity="danger" icon="pi pi-cancel" @click="showModal=false"/>


        </form>


     </div>



  </Modal>
    </div>
</template>
