<script setup>
    import { ref } from 'vue';
    import { usePollStore } from '@/stores/usePollStore.js';

    const emit = defineEmits(['close']);
    const { createPoll } = usePollStore();

    const title = ref('');
    const question = ref('');
    const options = ref(['', '']);
    const allowMultipleChoices = ref(false);
    const allowVoteChange = ref(false);
    const resultsPublic = ref(false);
    const duration = ref('');
    const addOption = () => options.value.push('');

    const submitPoll = async () => {
        await createPoll({ title: title.value, question: question.value, options: options.value, allowMultipleChoices: allowMultipleChoices.value, allowVoteChange: allowVoteChange.value, resultsPublic: resultsPublic.value, duration: duration.value }, false);
        emit('close');
    };

    const saveDraft = async () => {
        await createPoll({ title: title.value, question: question.value, options: options.value, allowMultipleChoices: allowMultipleChoices.value, allowVoteChange: allowVoteChange.value, resultsPublic: resultsPublic.value, duration: duration.value }, true);
        emit('close');
    };
</script>

<template>
    <form class="poll-form">
        <h2 class="form-title">Nouveau sondage</h2>

        <div class="field">
            <label>Titre <span class="optional">(optionnel)</span></label>
            <input v-model="title" type="text" placeholder="Titre du sondage" />
        </div>

        <div class="field">
            <label>Question <span class="required">*</span></label>
            <input v-model="question" type="text" placeholder="Quelle est votre question ?" />
        </div>

        <div class="field">
            <label>Options de réponse</label>
            <div v-for="(option, index) in options" :key="index" class="option-row">
                <span class="option-num">{{ index + 1 }}</span>
                <input v-model="options[index]" type="text" :placeholder="'Option ' + (index + 1)" />
            </div>
            <button type="button" class="btn-secondary" @click="addOption">+ Ajouter une option</button>
        </div>

        <div class="field">
            <label>Durée <span class="optional">(secondes, optionnel)</span></label>
            <input v-model="duration" type="number" min="0" placeholder="ex: 3600" />
        </div>

        <div class="checkboxes">
            <label class="checkbox-label">
                <input v-model="allowMultipleChoices" type="checkbox" />
                Autoriser le choix multiple
            </label>
            <label class="checkbox-label">
                <input v-model="allowVoteChange" type="checkbox" />
                Autoriser le changement de vote
            </label>
            <label class="checkbox-label">
                <input v-model="resultsPublic" type="checkbox" />
                Rendre les résultats publics
            </label>
        </div>

        <div class="actions">
            <button type="button" class="btn-draft" @click="saveDraft">Sauvegarder en brouillon</button>
            <button type="button" class="btn-submit" @click="submitPoll">Publier le sondage</button>
        </div>
    </form>
</template>

<style scoped>
    .poll-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .form-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #a78bfa;
        margin-bottom: 0.5rem;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .field label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #e2e8f0;
    }

    .field input[type="text"],
    .field input[type="number"] {
        background: #1e293b;
        border: 1px solid #4c1d95;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #e2e8f0;
        outline: none;
        transition: border-color 0.15s;
    }

    .field input:focus {
        border-color: #a78bfa;
        box-shadow: 0 0 0 2px rgba(167, 139, 250, 0.2);
    }

    .option-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .option-num {
        width: 1.5rem;
        text-align: center;
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .option-row input {
        flex: 1;
        background: #1e293b;
        border: 1px solid #4c1d95;
        border-radius: 0.375rem;
        padding: 0.4rem 0.75rem;
        font-size: 0.875rem;
        color: #e2e8f0;
        outline: none;
    }

    .option-row input:focus { border-color: #a78bfa; }

    .checkboxes {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #e2e8f0;
        cursor: pointer;
    }

    .optional { color: #64748b; font-weight: 400; }
    .required { color: #f87171; }

    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    button {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-secondary {
        background: none;
        color: #a78bfa;
        border: 1px dashed #4c1d95;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .btn-draft {
        background-color: #1e293b;
        color: #e2e8f0;
        border: 1px solid #334155;
    }

    .btn-draft:hover { background-color: #334155; }

    .btn-submit {
        background-color: #4c1d95;
        color: white;
    }

    .btn-submit:hover { background-color: #6d28d9; }
</style>
