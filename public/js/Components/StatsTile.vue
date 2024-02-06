<script setup>
import {ChartBarIcon} from '@vue-hero-icons/solid'
defineProps({ tile:String, Qty:Number });


</script>

<template>
     <div class="border-2 col-span-1 rounded-md border-cyan-600 w-full shadow-lg shadow-slate-400 object-fill text-center items-center text-gray-400 bg-lime-200 hover:border-red-300 ">
        <div class="m-1 capitalize tracking-wide  m-b-1 ">{{tile}}</div>
            <!-- <BeakerIcon class="h-6 w-6 text-blue-500"/> -->
        <div class="rounded-full bg-yellow-600  text-white m-4">{{ Qty }}</div>
    </div>
</template>
