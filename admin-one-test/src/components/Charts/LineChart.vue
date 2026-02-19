<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import {
  Chart,
  LineElement,
  PointElement,
  LineController,
  LinearScale,
  CategoryScale,
  Tooltip,
  Legend,
} from 'chart.js'
// 2. Accept data from the parent component
const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
})

const root = ref(null)

let chart
// 1. Tell Chart.js we want to use these features
Chart.register(
  LineElement,
  PointElement,
  LineController,
  LinearScale,
  CategoryScale,
  Tooltip,
  Legend
)
// 3. When the page is ready, DRAW on the canvas
onMounted(() => {
  chart = new Chart(root.value, {
    type: 'line',
    data: props.data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          display: false,
        },
        x: {
          display: true,
        },
      },
      plugins: {
        legend: {
          display: true,
          position: 'top',
          align: 'end',
          labels: {
            boxWidth: 12,
            usePointStyle: true,
            pointStyle: 'circle',
          },
        },
      },
    },
  })
})

const chartData = computed(() => props.data)
// 4. If data changes, update the lines smoothly
watch(chartData, (data) => {
  if (chart) {
    chart.data = data
    chart.update()
  }
})
</script>

<template>
  <canvas ref="root" />
</template>
