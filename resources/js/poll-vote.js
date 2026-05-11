//entrypoint page de vote

import './bootstrap';
import { createApp } from 'vue';
import App from './AppPollVote.vue';
import { router } from './router';

const el = document.getElementById('app');

createApp(App).use(router).mount(el);
