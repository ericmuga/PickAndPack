<template>
    <div>

         <div class="p-2 m-3">
           <div class="w-full p-3 tracking-wide text-center capitalize rounded-md bg-emerald-500 text-slate-700">#{{ assignment.id }} </div>
           <div>
                 <div class="max-w-2xl mx-4 mt-16 text-gray-900 bg-white rounded-lg shadow-xl sm:max-w-sm md:max-w-sm lg:max-w-sm xl:max-w-sm sm:mx-auto md:mx-auto lg:mx-auto xl:mx-auto">

                    <div class="mt-2 text-center">

                        <h2 class="font-semibold">{{assignment.assignee  }}</h2>
                        Ass. By :{{assignment.assignor  }}
                        <p>{{ assignment.time }}</p>
                    </div>

                    <ul class="flex items-center justify-around py-4 mt-2 text-gray-700">
                        <!-- <li class="flex flex-col items-center justify-around">
                        <svg class="w-4 text-blue-900 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <div>2k</div>
                        </li> -->
                        <li class="flex flex-col items-center justify-between">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>




                        <div>{{assignment.orders_count  }}</div>
                        </li>
                        <li class="flex flex-col items-center justify-around">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        <div>{{assignment.lines_count  }}</div>
                        </li>




                    </ul>
                     <div class="w-full p-3 m-2 text-center">
                            <span class="p-3 m-2 text-white" :class="(assignment.percentage==100)? 'bg-lime-800 rounded-full':'bg-rose-900   rounded-full'">
                                {{ (assignment.percentage==100)?'Complete!':'In Progress...' }}
                            </span>

                     </div>

                         <ProgressBar :value="assignment.percentage"></ProgressBar>
                    </div>




           </div>
         </div>

    </div>
</template>

<script setup>
import ProgressBar from 'primevue/progressbar';


const props = defineProps({
assignment : Object
})
</script>

<style lang="scss" scoped>

</style>
