//définition des routes Vue Router
//je comprends rien à ce fichier donc va falloir relire
import { createRouter, createWebHistory } from 'vue-router'
import VotePage from './components/VotePage.vue'

const routes = [
    { path: '/vote/:token', component: VotePage }
]

export const router = createRouter({
    history: createWebHistory(),
    routes
})
