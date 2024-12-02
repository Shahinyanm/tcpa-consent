import { createRouter, createWebHistory } from 'vue-router'
import CompaniesPage from '../components/CompaniesPage.vue'
import ConsentForm from '../components/ConsentForm.vue'

const routes = [
    {
        path: '/',
        name: 'Companies',
        component: CompaniesPage
    },
    {
        path: '/consent/:companyHash',
        name: 'ConsentForm',
        component: ConsentForm,
        props: true
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

export default router
