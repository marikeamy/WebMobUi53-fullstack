<script setup>

    import { ref, onMounted } from 'vue'
    import { useRoute } from 'vue-router'
    import { useFetchApi } from '@/composables/useFetchApi'

    const route = useRoute()
    const token = route.params.token
    const { fetchApi } = useFetchApi()
    const poll = ref(null)

    onMounted(async () => {
        poll.value = await fetchApi({ url: 'polls/' + token, method: 'GET' })
    })
</script>

<template>
    <p v-if="!poll">Aucun sondage.</p>
    <div v-else>
        <h1>{{ poll.title }}</h1>
        <p>{{ poll.question }}</p>
    </div>
</template>
