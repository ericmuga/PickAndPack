<script setup>

import { Link } from '@inertiajs/inertia-vue3';

const props=defineProps(['station'])

</script>
<template>
    <div>
        <!-- {{ station }} -->
       <ul>
        <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold"><Link :href="route('assignment.create',{'flag':'retail',station})">Retail</Link></li>
        <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold"><Link :href="route('assignment.create',{'flag':'horeca',station})" >Horeca</Link></li>
        <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold"><Link :href="route('assignment.create',{'flag':'upc',station})">UPC</Link></li>
        <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold"><Link :href="route('assignment.create',{'flag':'ff',station})">FF</Link></li>
        <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold"><Link :href="route('assignment.create',{station})">ALL</Link></li>
    </ul>
    </div>

</template>

