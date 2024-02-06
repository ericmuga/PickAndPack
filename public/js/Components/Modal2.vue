<script setup>

import { defineEmits, defineProps,ref } from 'vue';

defineProps({

    show:Boolean

});


</script>

<template>
    <div v-if="show" class="fixed inset-1 bg-black opacity-60 grid place-items-center" >

     <div class="p-1 bg-white text-black w-3/4 rounded max-w-2xl opacity-100">
        <header class="p-3 m-2">
         <slot name="header"> Default Heaer</slot>
        </header>

          <div class="p-3 m-2">
          <slot> Default Body</slot>
        </div>

        <hr/>
        <footer class="p-3 m-2" >
            <slot name="footer">
                footer
            </slot>
            <hr/>
        <Button label="Close"
          @click="$emit('close')"
         ></Button>
        </footer>
     </div>

</div>
</template>

<style>

</style>
