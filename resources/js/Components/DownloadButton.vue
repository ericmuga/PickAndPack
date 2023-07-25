
<script setup>

  defineProps({
    link:String
  })
</script>

<template>
    <div>
        <a
         :href="link"
        >
            <Button label="Download" icon="pi pi-download" severity="success" />
        </a>

    </div>
</template>
