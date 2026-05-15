<script setup>
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Tooltip } from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip)

const props = defineProps({
    options: { type: Array, required: true }
})

const chartData = computed(() => ({
    labels: props.options.map(o => o.label),
    datasets: [{
        data: props.options.map(o => o.votes_count),
        backgroundColor: 'rgba(20, 184, 166, 0.7)',
        borderColor: 'rgba(20, 184, 166, 1)',
        borderWidth: 1,
        borderRadius: 4,
    }]
}))

const chartOptions = {
    responsive: true,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
        }
    }
}
</script>

<template>
    <Bar :data="chartData" :options="chartOptions" />
</template>
