<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Companies</h1>
        <div v-if="loading" class="text-center">
            <p class="text-xl">Loading companies...</p>
        </div>
        <div v-else-if="error" class="text-center text-red-600">
            <p class="text-xl">{{ error }}</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="company in companies" :key="company.hash" class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-2">{{ company.name }}</h2>
                <p class="text-gray-600 mb-4">{{ company.description }}</p>
                <router-link
                    :to="{ name: 'ConsentForm', params: { companyHash: company.hash }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                >
                    Open Consent Form
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
    name: 'CompaniesPage',
    setup() {
        const companies = ref([])
        const loading = ref(true)
        const error = ref(null)

        const fetchCompanies = async () => {
            try {
                const response = await axios.get('/api/companies')
                companies.value = response.data
                loading.value = false
            } catch (err) {
                error.value = 'Failed to load companies. Please try again later.'
                loading.value = false
            }
        }

        onMounted(fetchCompanies)

        return {
            companies,
            loading,
            error
        }
    }
}
</script>
