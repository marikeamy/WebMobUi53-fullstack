//entrypoint page de résultats

import './bootstrap';
import { createApp } from 'vue';
import App from './AppPollResults.vue';
import { router } from './router';

const el = document.getElementById('app');

createApp(App).use(router).mount(el);
