//définition des routes Vue Router
//je comprends rien à ce fichier donc va falloir relire
import { createRouter, createWebHistory } from 'vue-router'
import VotePage from './components/VotePage.vue'
import ResultsPage from './components/ResultsPage.vue'

const routes = [
    { path: '/vote/:token', component: VotePage },
    { path: '/results/:id', component: ResultsPage}
]

export const router = createRouter({
    history: createWebHistory(),
    routes
})
