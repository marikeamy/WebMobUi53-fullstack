<script setup>
  import PollTable from './components/PollTable.vue';
  import { usePollStore } from '@/stores/usePollStore';
  import { onMounted } from 'vue';
  import { useFetchApi } from '@/composables/useFetchApi'

  const { fetchApi } = useFetchApi()
  onMounted(async () => {
      const polls = await fetchApi({ url: 'polls', method: 'GET' })
      setPolls(polls)
  })

  const props = defineProps({
    polls: { type: Array, default: () => [] },
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
  });

  const { setPolls } = usePollStore();
  setPolls(props.polls);
</script>

<template>
  <PollTable />
</template>
