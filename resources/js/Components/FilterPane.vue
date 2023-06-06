<template>
    <div class="p-5  ">
      <form class="" @submit.prevent="postForm()">
        <div v-for="(value, key) in members" :key="key" class="">
          <!-- <label class="mx-1 capitalize col-span-1">{{ key }}</label> -->


           <div v-if="value==='string'">
             <InputText
               class="capitalize"
               :v-model="dynamicObject.key"
               :placeholder="key"
             />
           </div>
           <div v-else-if="value ==='datetime'">

            <Calendar  v-model="members[key]" showIcon selectionMode="range" />

           </div>
           <div v-else-if="value ==='time'" >

              <input type="time" v-model="members[key]" />

            </div>
            <div v-else-if="value ==='date'" >

                <Calendar v-model="members[key]" selectionMode="range"  :manualInput="true" />

            </div>

            <!-- <component class="col-span-1" :is="getInputType(value)" :name="key" v-model="formData[key]" /> -->
          <hr/>

        </div>
        <div class="text-center">
            <Button type="Submit" label="Search"/>
        </div>

      </form>
    </div>
  </template>

  <script setup>
  import { toRefs,ref, computed, defineProps,watch } from 'vue';
  import { useForm } from '@inertiajs/inertia-vue3';
  import Calendar from 'primevue/calendar';
  import { Inertia } from '@inertiajs/inertia';

  const dates = ref();


  const props=defineProps({ members:Object,
                targetRoute:String
              })
 const dynamicObject =ref({})


  const dynamicFormProps = {
    members: {
      type: Object,
      required: true,
    },
  };


  </script>

