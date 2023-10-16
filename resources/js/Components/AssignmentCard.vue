<template>
    <div>

         <div class="p-2 m-3">
           <div class="w-full p-3 tracking-wide text-center capitalize bg-teal-300 rounded-md text-slate-700">#{{ assignment.id }} </div>
           <div>{{ assignment }}</div>
         </div>

    </div>
</template>

<script setup>

const props = defineProps({
assignment : Object
})
</script>

<style lang="scss" scoped>

</style>
