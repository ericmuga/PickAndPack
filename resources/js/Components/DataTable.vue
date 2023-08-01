<script setup>
//   import { stringify } from 'postcss';
import {defineProps} from 'vue'
import SearchBox from '@/Components/SearchBox.vue'
import Pagination from '@/Components/Pagination.vue'
import Toolbar from 'primevue/toolbar';
  defineProps({


      searchUrl:String,
      headers: {
      type: Object,
      required: true,
    },
    items: {
      type: Object,
      required: true,
    },


  });

  const formatNumberWithThousandSeparator = (value) => {
  const numberValue = Number(value);
  if (isNaN(numberValue)) {
    return value; // Return the original value if it's not a valid number
  } else {
    return numberValue.toLocaleString(); // Format the number with the thousand separator
  }
};
</script>

<template>
    <div>
        <Toolbar>
                 <template #start>

                    </template>
                    <template #center>
                        <!-- <div class="flex flex-row">
                            <Pagination :links="items.links" />
                        </div> -->

      <SearchBox :model="searchUrl" class="-mx-4 text-xs"/>

                    </template>

                                <!-- <template #end> -->

                                    <!-- <Button type="button" rounded disabled label="Total Lines"  :badge=props.orderLines.meta.total badgeClass="p-badge-danger" outlined class="justify-end" /> -->



                                <!-- </template> -->
                            </Toolbar>







                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <div class="flex flex-row w-full ">




                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr class="bg-slate-300">
                                        <th v-for="(header, key) in headers" :key="key" scope="col" class="px-2 py-1">{{ header }}</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(item, index) in items.data" :key="index" class="bg-white border-b dark:bg-gray-900 dark:border-gray-700 hover:text-white hover:bg-slate-400">
                                        <td v-for="(header, key) in headers" :key="key" class="text-xs text-center ">
                                        {{ formatNumberWithThousandSeparator(item[header]) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                    </table>
                                    </div>
                                    </div>



                            <Toolbar>
                                <template #center>
                                    <Pagination :links="items.links" />

                                </template>
                            </Toolbar>

    </div>
</template>



<style lang="scss" scoped>

</style>
