<script setup>
  import PollTable from './components/PollTable.vue';
  import { usePollStore } from '@/stores/usePollStore';
  import { onMounted } from 'vue';
  import { useFetchApi } from '@/composables/useFetchApi';

  const props = defineProps({
    polls: { type: Array, default: () => [] },
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
  });

  const { setPolls } = usePollStore();
  const { fetchApi } = useFetchApi();

  setPolls(props.polls);

  onMounted(async () => {
    const result = await fetchApi({ url: 'polls/' });
    if (result) setPolls(result);
  });
</script>

<template>
  <PollTable />
</template>
