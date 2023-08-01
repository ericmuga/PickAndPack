<template>

    <canvas ref="chartCanvas" width="300" height="300"></canvas>
  </template>

  <script setup>
  import { ref, onBeforeUpdate, onMounted, defineExpose } from 'vue';

  const chartData = ref({
    // labels: ['Red', 'Blue', 'Yellow','Green'],
    labels: props.top5Labels,
    datasets: [
      {
        label: 'My Dataset',
        // data: [10, 20, 30,40],
        data: props.top5Weights,
        // backgroundColor: ['#0ccf08', '#55ad9a', '#a8314b'],
      },
    ],
  });

  const props=defineProps({
   top5Labels:Object,
   top5Weights:Object,
//    chartTitle:String,

  });

  const chartInstance = ref(null);

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
  };

  // Move chartCanvas declaration inside setup
  const chartCanvas = ref(null);

  onBeforeUpdate(() => {
    if (chartInstance.value) {
      chartInstance.value.data = chartData.value;
      chartInstance.value.update();
    }
  });

  onMounted(() => {
    // Assign the DOM element to chartCanvas
    chartCanvas.value = document.querySelector('canvas');
    const ctx = chartCanvas.value.getContext('2d');
    chartInstance.value = new Chart(ctx, {
      type: 'pie',
      data: chartData.value,
      options: chartOptions,
    });
  });

  defineExpose({ chartCanvas });
  </script>

  <script>
  import Chart from 'chart.js/auto'; // Import the Chart class

  // The script setup block is only for reactive variables, use the regular script block for imports and other logic
  </script>
