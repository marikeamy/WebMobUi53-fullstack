<script setup>
    import { usePollStore } from '@/stores/usePollStore';
    import PollForm from './PollForm.vue';
    import { ref } from 'vue';

    const { polls, deletePoll, startPoll } = usePollStore();
    const showCreateForm = ref(false);
    const pollAModifier = ref('');
    const copied = ref(null);

    async function delPoll(id) {
        console.log('delete Poll ID:', id);
        await deletePoll(id);
    }

    async function modPoll(poll) {
        showCreateForm.value = true;
        pollAModifier.value = poll;
    }

    const formatDate = (dateStr) => {
        if (!dateStr) return '-'
        return new Date(dateStr).toLocaleString('fr-CH', { dateStyle: 'short', timeStyle: 'short' })
    }

    //Fonction pour le bouton "copier le lien de partage"
    async function copyLink(poll){
        if (!poll) return ;
        const url = `${window.location.origin}/vote/${poll.secret_token}`;
        //navigator.clipboard est une API native moderne qui copie du texte dans le presse-papier depuis le navigateur
        navigator.clipboard.writeText(url);
        copied.value = poll.id;
        setTimeout(() => { copied.value = null; }, 2000);
    }
</script>

<template>
<!-- create form button
 On réinitialise pollAModifier pour ne pas avoir les données d'un sondage modifié auparavant-->
<button id="add-btn" @click="showCreateForm = true; pollAModifier=null">Créer un sondage</button>

<!-- modal overlay -->
<div v-if="showCreateForm" class="modal-overlay" @click.self="showCreateForm = false">
  <div class="modal-box">
    <PollForm @close="showCreateForm = false" :poll="pollAModifier"/>
  </div>
</div>

<!-- table with all forms -->
<p v-if="polls.length === 0">Aucun sondage.</p>

  <table v-else class="w-full border-collapse text-left">
    <thead>
      <tr>
        <th class="border px-3 py-2">Actions</th>
        <th class="border px-3 py-2">Titre</th>
        <th class="border px-3 py-2">Question</th>
        <th class="border px-3 py-2">Brouillon</th>
        <th class="border px-3 py-2">Résultats</th>
        <th class="border px-3 py-2">Debut</th>
        <th class="border px-3 py-2">Fin</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="poll in polls" :key="poll.id">
        <td class="border px-3 py-2">
            <button type="button" id="delete-btn" @click="delPoll(poll.id)">Supp.</button>
            <button type="button" id="modify-btn" @click="modPoll(poll)">Modifier</button>
            <a :href="`/results/${poll.id}`" class="btn-link">Résultats</a>
            <button v-if="poll.is_draft" id="start-btn" @click="startPoll(poll.id)">Lancer</button>
            <button v-if="!poll.is_draft && copied !== poll.id" id="btn-sharelink" @click="copyLink(poll)">Copier le lien de partage</button>
            <button v-else-if="!poll.is_draft && copied === poll.id" id="btn-sharelink">Lien copié !</button>
        </td>
        <td class="border px-3 py-2">{{ poll.title || '-' }}</td>
        <td class="border px-3 py-2">{{ poll.question }}</td>
        <td class="border px-3 py-2">{{ poll.is_draft ? 'Oui' : 'Non' }}</td>
        <td class="border px-3 py-2">{{ poll.results_public ? 'Public' : 'Privé' }}</td>
        <td class="border px-3 py-2">{{ formatDate(poll.started_at) }}</td>
        <td class="border px-3 py-2">{{ formatDate(poll.ends_at) }}</td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
  button {
    color: white;
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    margin-bottom: 10px;
  }

  .btn-link {
    display: inline-block;
    color: white;
    background-color: rgb(80, 80, 80);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    text-decoration: none;
    margin-bottom: 10px;
  }

  #delete-btn    { background-color: rgb(151, 24, 24); }
  #modify-btn    { background-color: rgb(25, 75, 151); }
  #start-btn     { background-color: rgb(31, 148, 11); }
  #add-btn       { background-color: rgb(31, 148, 11); }
  #btn-sharelink { background-color: rgb(13, 148, 136); }

  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
  }

  .modal-box {
    background: #0f172a;
    border: 1px solid #4c1d95;
    border-radius: 0.5rem;
    padding: 1.5rem;
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
  }
</style>
