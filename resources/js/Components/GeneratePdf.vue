<template>
    <div>
      <Button @click="generatePdf">Generate PDF</Button>
    </div>
  </template>

  <script setup>
  import axios from 'axios';

   const props= defineProps({data:Object})

   const  generatePdf=()=> {
        // Make an AJAX request to generate the PDF

       axios.post('/generate-pdf', props.data, { responseType: 'blob' }).then((response) => {
    // Create a Blob from the PDF data
    const blob = new Blob([response.data], { type: 'application/pdf' });
    const url = window.URL.createObjectURL(blob);

    // Open the PDF in a new tab
    window.open(url);
});
      }
  </script>
