<script setup>
  import { Link } from '@inertiajs/inertia-vue3';
  import { Inertia } from '@inertiajs/inertia';

  const props = defineProps(['station']);

  const load = (route) => {
    Inertia.visit(route);
  };
</script>

<template>
  <div>
    <ul>
      <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold" @click="load(route('assignment.create',{'flag':'retail',station}))">Retail</li>
      <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold" @click="load(route('assignment.create',{'flag':'horeca',station}))">Horeca</li>
      <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold" @click="load(route('assignment.create',{'flag':'upc',station}))">UPC</li>
      <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold" @click="load(route('assignment.create',{'flag':'ff',station}))">FF</li>
      <li class="text-center rounded-md hover:bg-slate-400 hover:text-white hover:font-bold" @click="load(route('assignment.create',{station}))">ALL</li>
    </ul>
  </div>
</template>
