



<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import Toolbar from 'primevue/toolbar';
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import {watch, ref} from 'vue';
import Pagination from '@/Components/Pagination.vue'
import Swal from 'sweetalert2'
import Modal from '@/Components/Modal.vue'
import Drop from '@/Components/Drop.vue'



const form= useForm({
     'assignee':'',
     'order_parts':[],
     'part':''
     
})

const parts=['A','B','C','D']

const props=defineProps({
       order_parts:Object,
       assemblers:Object,
       ass:Object
  })


let filterPart=ref({})

const filterParts=(character) =>{
filterPart.value= props.order_parts.filter((item) => {
    const orderPart = item.order_part;
    const lastChar = orderPart.charAt(orderPart.length - 1); // Get the last character

    // Check if the last character matches the specified character
    return lastChar === character;
  });
}


const createOrUpdateItem=()=>{
   
    if (mode.state=='Create')
          form.post(route('assignment.store'))
        else
     form.patch(route('assignment.update',form.item_no))
      showModal.value=false;
    Swal.fire(`Assignment ${mode.state}ed Successfully!`,'','success');

}


let mode= { state: 'Create' };

  
  let showModal=ref(false);


const showCreateModal=()=>{

    mode.state='Create'
    form.reset()  
    totalOrderQty.value=0;
    totalLines.value=0;
    showModal.value=true

}




const showUpdateModal=(item)=>{

    mode.state='Update'
    // alert(mode.state)

    form.assignee=item.assignee
    form.orders_parts=item.order_parts

    showModal.value=true
}



const sumOrderQtyByOrderPart=(dataArray, searchOrderPart)=> {
  let totalOrderQty = 0;

  for (const item of dataArray) {
    if (item.order_part === searchOrderPart) {
      totalOrderQty += parseFloat(item.order_qty);
    }
  }

  return totalOrderQty;
}

const sumOrderLineCountByOrderPart=(dataArray, searchOrderPart)=> {
  let totalOrderQty = 0;

  for (const item of dataArray) {
    if (item.order_part === searchOrderPart) {
      totalOrderQty += parseFloat(item.line_count);
    }
  }

  return totalOrderQty;
}

let totalOrderQty=ref(0);
let totalLines=ref(0);

const sumLines=()=>{ totalOrderQty.value=0; totalLines.value=0
                      for (var i = form.order_parts.length - 1; i >= 0; i--) 
                       {
                         totalOrderQty.value+=sumOrderQtyByOrderPart(props.order_parts,form.order_parts[i])
                         totalLines.value+=sumOrderLineCountByOrderPart(props.order_parts,form.order_parts[i]);
                       } 
                     // console.log(totalOrderQty)
                  };






</script>


<template>
    <Head title="Items"/>

    <AuthenticatedLayout @add="showModal=true">
        <template #header>
           
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
                                        <!-- <i class="mr-2 pi pi-bars p-toolbar-separator" /> -->
                                        <!-- <SplitButton label="Save" icon="pi pi-check" :model="items" class="p-button-warning"></SplitButton> -->
                                    <Button
                                         label="Assign"
                                         icon="pi pi-plus"
                                         severity="success"
                                         @click="showCreateModal()"
                                         rounded
                                    ></Button>
                                </template>
                                <template #center>
                                    <div>
                                    <!--    <Pagination :links="items.meta.links" /> -->
                                    </div>
                                    <!-- <Modal :show="showModal.value">
                                        <FilterPane :propsData="columnListing" />
                                    </Modal> -->
                                      <!-- <FilterPane :propsData="columnListing" /> -->

                                </template>

                                    <template #end>

                                       <!--
                                        <a :href="route('items.download')" class="">
                                            <Button icon="pi pi-download" severity="primary" text raised rounded label="Items"/>
                                        </a>
                                        -->




                                            <!-- <SearchBox model="items.index" /> -->
                                    </template>
                                        </Toolbar>

                                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                                    <tr class="bg-white text-black hover:bg-gray-300 font-semibold">
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                           #
                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                           Assignee
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Order 
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Part
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Assignor
                                                        </th>
                                                         <th scope="col" class="px-6 py-3">
                                                            Time
                                                        </th>
                                                        
                                                        <th scope="col" class="px-6 py-3">
                                                           Actions
                                                        </th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="item in ass.data" :key="item.item_no"
                                                    class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">

                                                   

                                                    <td class="px-3 py-2 text-xs font-bold text-center ">
                                                        {{ item.assignee }}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.order}}
                                                    </td>

                                                     <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.part}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.assignor}}
                                                    </td>
                                                    <td class="px-3 py-2 text-xs font-bold">
                                                        {{ item.time}}
                                                    </td>
                                                    <td>
                                                       <div class="flex flex-row">
                                                          <Drop  :drop-route="route('assignment.destroy',item.id)"/>
                                                            <Button
                                                                      icon="pi pi-pencil"
                                                                      severity="info"
                                                                      text


                                                                      @click="showUpdateModal(item)"
                                                                      />
                                                       </div>
                                                    </td>

                                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <Toolbar>
                        <template #center>
                            <div >
                                <!--<Pagination :links="items.meta.links" /> -->
                            </div>
                        </template>
                    </Toolbar>


                </div>




                <!--end of stats bar-->

            </div>
        </div>
    </div>
</div>

   <Modal :show="showModal" @close="showModal=false" >

     <div class="flex flex-col p-4 rounded-sm">

        <div  class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> {{mode.state}} Assignment</div>
        <!-- <div v-else class="w-full p-2 mb-2 tracking-wide text-center text-white rounded-sm bg-slate-500"> Update Assignment</div> -->

          <form  @submit.prevent="createOrUpdateItem()">

<div class="flex flex-col justify-center gap-3">


        <Dropdown 
         :options=assemblers
         optionLabel='name'
         optionValue='id'
         v-model="form.assignee"
         filter
        />


        <Dropdown 
         :options=parts
         @change="filterParts(form.part)"
        
         v-model="form.part"
         filter
        />

        <MultiSelect
         :options=filterPart
         optionLabel='description'
         optionValue='order_part'
         v-model="form.order_parts"
         @change="sumLines()"
         filter
        
         />

         <div class="flex justify-center p-5 m-4 rounded bg-slate-500 space-x-4">
          Quantity :<Button :label=totalOrderQty />
          Lines :<Button :label=totalLines />
        </div>
        <Button
          severity="success"
          icon="pi pi-send"
          type="submit"
          :label=mode.state

        />

        
        <Button label="Cancel" severity="warning" icon="pi pi-cancel" @click="showModal=false"/>
</div>

    </form>

     </div>

  </Modal>
</AuthenticatedLayout>

</template>
<style lang="scss" scoped>

</style>
