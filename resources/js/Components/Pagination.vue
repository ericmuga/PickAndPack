<template>
    <div>
        <!--pagination -->
        <div class="p-3 mt-6 text-black">
            <Link     v-for="link in links" :key=link.url
                      v-html="link.label"
                      :href="refinedURL(link.url)"
                      :class="{'text-slate-500 hover:bg-none':!link.url,'font-bold underline':link.active}"
                      preserveScroll
                      class="p-2 border-2 border-white border-sm hover:bg-slate-200 hover:font-bold"
                      >
            </Link>
        </div>
    </div>
</template>

<script>

import {computed, ref} from 'vue'
export default {
    props:{
        links:Object,
        prefix:String,
    },
   setup(props){

    //    const resource= props.resource.value
       const links=props.links
       const prefix= ref(props.prefix)
       const url=ref(props.url);
       const refinedURL=(link)=>(`${prefix.value}/${link}`)

        return {
              refinedURL
        };
    },

};
</script>

<style lang="scss" scoped>

</style>
