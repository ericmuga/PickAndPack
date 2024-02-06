<script setup>
import { onMounted, ref } from 'vue';
import Chart from 'chart.js/auto'; // Import Chart.js

const chartRef = ref(null);
const props=defineProps(['labels','values'])

onMounted(() => {
  const ctx = chartRef.value.getContext('2d');

  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: props.labels,
      datasets: [
        {
          label: 'Top 5 SKUs',
        //   backgroundColor: 'rgba(75, 192, 192, 0.2)',
          backgroundColor: ['#e0584f','#9360d1','#beed9f','#f5cf84','#de8cbe'],
          borderColor: 'rgba(75, 192, 192, 1)',
        //   borderWidth: 1,
          data: props.values,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
    },
  });
});

</script>

<template>
  <div class="p-5 m-5 shadow-md">
    <canvas ref="chartRef" width="400" height="400"></canvas>
  </div>
</template>
