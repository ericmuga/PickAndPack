<script setup>

import {computed, ref} from 'vue'
// import { Link } from '@inertiajs/vue3'
import { Link} from '@inertiajs/inertia-vue3'
defineProps({
    links:Object,
})


</script>

<template>
    <div>
        <!--pagination -->
        <div class="p-3 mt-6 text-black">
            <Link    v-for="link in links" :key=link.url
                      v-html="link.label"
                      :href="link.url"
                      :class="{'text-slate-500 hover:bg-none':!link.url,'font-bold underline':link.active}"
                      preserveScroll
                      class="p-2 border-2 border-white border-sm hover:bg-slate-200 hover:font-bold"
                      >
            </Link>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
