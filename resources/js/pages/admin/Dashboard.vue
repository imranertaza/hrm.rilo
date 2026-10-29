<template>
    <DashboardHeader title="Welcome to Admin Dashboard" />

    <div class="row">



    </div>
</template>

<script setup>
import DashboardHeader from '@/components/DashboardHeader.vue';
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({})

const fetchStats = async () => {
    try {
        const res = await axios.get('/api/dashboard')
        stats.value = res.data.data
    } catch (error) {
        console.error('Failed to load stats', error)
    }
}

onMounted(fetchStats)
</script>
