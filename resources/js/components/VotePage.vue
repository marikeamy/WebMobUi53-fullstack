<script setup>

    import { ref, onMounted, onUnmounted, computed } from 'vue'
    import { useRoute } from 'vue-router'
    import { useFetchApi } from '@/composables/useFetchApi'
    import PollChart from './PollChart.vue'

    const route = useRoute()
    const token = route.params.token
    const { fetchApi } = useFetchApi()
    const { fetchApi: fetchApiUser} = useFetchApi('/api')
    const poll = ref(null)
    const selectedOption = ref(null);
    const hasVoted = ref(false);
    const isClosed = computed(() => poll.value && new Date(poll.value.ends_at) < new Date());
    const isAuthenticated = ref(null)
    let interval = null;

    onMounted(async () => {
        poll.value = await fetchApi({ url: 'polls/' + token, method: 'GET' })
        try{
            isAuthenticated.value = await fetchApiUser({url : '/user', method: 'GET'})
        }catch{
            isAuthenticated.value = false;
        }
        if(isClosed.value){
            startPolling();
        }
    })
    onUnmounted(() => clearInterval(interval));

    const totalVotes = computed(() =>
        poll.value ? poll.value.options.reduce((sum, o) => sum + o.votes_count, 0) : 0
    )

    const pct = (votes_count) => totalVotes.value === 0 ? 0 : Math.round(votes_count / totalVotes.value * 100)

    const submitVote = async () => {
        const result = await fetchApi({url:`options/${selectedOption.value}/vote`, method:"POST"});
        hasVoted.value = true;
        startPolling();
    };

    //Fonction pour le polling en temps réel
    function startPolling() {
        interval = setInterval(async () => {
            poll.value = await fetchApi({ url: 'polls/' + token, method: 'GET' })
        }, 1000);
    }

</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex items-center justify-center px-4 py-8">

        <!-- Chargement -->
        <p v-if="!poll || isAuthenticated === null" class="text-slate-500 dark:text-slate-400">Chargement...</p>

        <!-- Non connecté + résultats non publics -->
        <div v-else-if="!isAuthenticated && !poll.results_public" class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8 text-center">
            <p class="text-lg font-semibold text-slate-700 dark:text-slate-200 mb-2">Résultats non publics</p>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Les résultats de ce sondage ne sont pas accessibles publiquement.</p>
        </div>

        <!-- Non connecté + résultats publics -->
        <div v-else-if="!isAuthenticated && poll.results_public" class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ poll.title }}</h1>
            <p class="text-slate-600 dark:text-slate-300 mb-2">{{ poll.question }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ totalVotes }} vote{{ totalVotes !== 1 ? 's' : '' }} au total</p>
            <div class="space-y-4">
                <div v-for="option in poll.options" :key="option.id" class="p-3 rounded-lg border border-slate-200 dark:border-slate-600">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ option.label }}</span>
                        <span class="text-slate-500 dark:text-slate-400">{{ pct(option.votes_count) }}% ({{ option.votes_count }})</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                        <div class="h-2 rounded-full bg-teal-500 transition-all duration-500" :style="{ width: pct(option.votes_count) + '%' }"></div>
                    </div>
                </div>
            </div>
            <PollChart :options="poll.options" />
        </div>

        <!-- Formulaire de vote (connecté) -->
        <div v-else-if="isAuthenticated && !hasVoted && !isClosed" class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ poll.title }}</h1>
            <p class="text-slate-600 dark:text-slate-300 mb-6">{{ poll.question }}</p>

            <fieldset class="space-y-3 mb-8">
                <legend class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Options</legend>
                <label
                    v-for="option in poll.options"
                    :key="option.id"
                    class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-600 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition"
                    :class="{ 'border-teal-500 bg-teal-50 dark:bg-teal-900/20': selectedOption === option.id }"
                >
                    <input v-model="selectedOption" type="radio" name="options" :value="option.id" class="accent-teal-600">
                    <span class="text-slate-800 dark:text-slate-200">{{ option.label }}</span>
                </label>
            </fieldset>

            <button
                type="button"
                @click="submitVote"
                :disabled="!selectedOption"
                class="w-full py-2 px-4 rounded-md text-white font-semibold transition"
                :class="selectedOption ? 'bg-teal-600 hover:bg-teal-700 cursor-pointer' : 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed'"
            >
                Voter
            </button>
        </div>

        <!-- Sondage fermé : deux colonnes -->
        <div v-else-if="isClosed && !hasVoted" class="w-full max-w-2xl bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden">
            <div class="flex flex-col sm:flex-row">

                <!-- Colonne gauche : statut -->
                <div class="sm:w-1/3 bg-red-50 dark:bg-red-900/20 border-b sm:border-b-0 sm:border-r border-red-200 dark:border-red-800 p-8 flex flex-col items-center justify-center text-center gap-3">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 uppercase tracking-wide">Fermé</span>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Terminé le<br>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ new Date(poll.ends_at).toLocaleDateString('fr-CH') }}</span>
                    </p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">{{ totalVotes }} vote{{ totalVotes !== 1 ? 's' : '' }}</p>
                </div>

                <!-- Colonne droite : résultats -->
                <div class="flex-1 p-8">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white mb-1">{{ poll.title }}</h1>
                    <p class="text-slate-600 dark:text-slate-300 mb-6">{{ poll.question }}</p>
                    <div class="space-y-4">
                        <div v-for="option in poll.options" :key="option.id" class="p-3 rounded-lg border border-slate-200 dark:border-slate-600">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ option.label }}</span>
                                <span class="text-slate-500 dark:text-slate-400">{{ pct(option.votes_count) }}% ({{ option.votes_count }})</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                                <div class="h-2 rounded-full bg-slate-400 dark:bg-slate-500 transition-all duration-500" :style="{ width: pct(option.votes_count) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Résultats après vote -->
        <div v-else class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8">
            <p class="text-sm font-semibold text-teal-600 dark:text-teal-400 mb-4">Merci pour votre vote !</p>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ poll.title }}</h1>
            <p class="text-slate-600 dark:text-slate-300 mb-6">{{ poll.question }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ totalVotes }} vote{{ totalVotes !== 1 ? 's' : '' }} au total</p>
            <div class="space-y-4">
                <div v-for="option in poll.options" :key="option.id"
                    class="p-3 rounded-lg border transition"
                    :class="option.id === selectedOption ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20' : 'border-slate-200 dark:border-slate-600'"
                >
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-slate-800 dark:text-slate-200">
                            {{ option.label }}
                            <span v-if="option.id === selectedOption" class="ml-1 text-teal-600 dark:text-teal-400 text-xs">✓ votre choix</span>
                        </span>
                        <span class="text-slate-500 dark:text-slate-400">{{ pct(option.votes_count) }}% ({{ option.votes_count }})</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500"
                            :class="option.id === selectedOption ? 'bg-teal-500' : 'bg-slate-400 dark:bg-slate-500'"
                            :style="{ width: pct(option.votes_count) + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <PollChart :options="poll.options" />
        </div>

    </div>
</template>
