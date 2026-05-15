<script setup>

    import { ref, onMounted, computed } from 'vue'
    import { useRoute } from 'vue-router'
    import { useFetchApi } from '@/composables/useFetchApi'

    const route = useRoute()
    const { fetchApi } = useFetchApi()
    const poll = ref(null)
    //isClosed peut être utile pour indiquer que c'est fermé, même s'il n'y pas d'interactionm
    const isClosed = computed(() => poll.value && new Date(poll.value.ends_at) < new Date());

    onMounted(async () => {
        poll.value = await fetchApi({ url: `polls/${route.params.id}/results`, method: 'GET' })
    })

    //count totalVotes pour l'affichage
    const totalVotes = computed(() =>
        poll.value ? poll.value.options.reduce((sum, o) => sum + o.votes_count, 0) : 0
    )

    const pct = (votes_count) => totalVotes.value === 0 ? 0 : Math.round(votes_count / totalVotes.value * 100)

</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex items-center justify-center px-4 py-8">

        <!-- Chargement -->
        <p v-if="!poll" class="text-slate-500 dark:text-slate-400">Pas de sondage correspondant.</p>

        <!-- Résultats -->
        <div v-else class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ poll.title }}</h1>
            <p class="text-slate-600 dark:text-slate-300 mb-6">{{ poll.question }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ totalVotes }} vote{{ totalVotes !== 1 ? 's' : '' }} au total</p>
            <div class="space-y-4">
                <div v-for="option in poll.options" :key="option.id"
                    class="p-3 rounded-lg border transition"
                >
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-800 dark:text-slate-200">
                            {{ option.label }}
                        </span>
                        <span class="text-slate-500 dark:text-slate-400">{{ pct(option.votes_count) }}% ({{ option.votes_count }})</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500 bg-teal-500"
                            :style="{ width: pct(option.votes_count) + '%' }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
