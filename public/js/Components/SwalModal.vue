<template>
    <div>
      <!-- Modal content -->
      <div id="pdf-modal">
        <iframe :src="pdfDataUrl" width="100%" height="400px"></iframe>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue';
  import Swal from 'sweetalert2';

  const props = defineProps(['pdfDataUrl']);



  onMounted(() => {
    Swal.fire({
      html: '<div id="pdf-modal"></div>',
      showCloseButton: true,
      customClass: {
        content: 'pdf-modal-content',
      },
    });
  });
  </script>

  <style scoped>
  .pdf-modal-content {
    padding: 0;
  }
  </style>
