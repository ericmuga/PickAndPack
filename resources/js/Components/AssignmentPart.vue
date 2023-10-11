<template>
      <td class="p-1 px-3 py-2 text-xs text-center flex-col " v-if="order.part_a!=0">

                                                      
        <Button  v-show="order.confirm_a" icon="pi pi-eye" severity="warning" 
        :badge=order.part_a text raised rounded aria-label="Notification" 
        @click="showLines(order.order_no,'A')"
        
        />
        
       <span class="p-2 m-1 rounded-full text-teal-500" 
        v-show="checkSelected({'order_no':order.order_no,'part':'A'})"> 
            Selected
        </span> 

    </td>

</template>

<script setup>

 
</script>

<style lang="css" scoped>
</style>