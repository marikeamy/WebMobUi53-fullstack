import { ref } from 'vue';
import { useFetchApi } from '@/composables/useFetchApi';
import PollForm from '@/components/PollForm.vue';

const polls = ref([]);

export function usePollStore() {
  const { fetchApi } = useFetchApi();

  function setPolls(data) {
    polls.value = data;
  }

  async function deletePoll(id) {
    const result = await fetchApi({ url: 'polls/' + id, method: 'DELETE' });
    if (result) {
      polls.value = polls.value.filter(p => p.id !== id);
    }
  }

  async function modifyPoll(id, {title, question, options, duration, allowMultipleChoices, allowVoteChange, resultsPublic}, is_draft) {
    //result contient la réponse du serveur avec le poll mis à jour.
    const result = await fetchApi({ url: 'polls/' + id, method: 'PATCH', data: {title:title, question:question, options:options, duration:duration, allowMultipleChoices:allowMultipleChoices, allowVoteChange:allowVoteChange, resultsPublic:resultsPublic, is_draft:is_draft}});
    if (result) {
        //on modifie la valeur réactive avec le poll mis à jour
        //on checke dans le tableau si l'id correspond au poll modifié, si c'est le cas, on l'update avec le result
      polls.value = polls.value.map(p => p.id === id ? result : p);
    }
  }

  //On prend tous les paramètres du formulaire
  async function createPoll({title, question, options, duration, allowMultipleChoices, allowVoteChange, resultsPublic}, is_draft) {
    //On les passe au fetch
    const result = await fetchApi({url:'polls/', method:"POST", data: {title:title, question:question, options:options, duration:duration, allowMultipleChoices:allowMultipleChoices, allowVoteChange:allowVoteChange, resultsPublic:resultsPublic, is_draft:is_draft}});
    if (result) {
        polls.value.push(result);
    }
  }

  return { polls, setPolls, deletePoll, modifyPoll, createPoll };
}
