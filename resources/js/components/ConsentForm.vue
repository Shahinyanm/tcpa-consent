<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-md">
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    {{ formTitle }}
                </h2>
                <button class="text-indigo-600 hover:text-indigo-800" @click="goBack">
                    Back
                </button>
            </div>

            <div v-if="company" class="text-center text-gray-600 mb-4">
                Company: {{ company.name }}
            </div>

            <form v-if="!consentId" class="mt-8 space-y-6" @submit.prevent="submitForm">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <input v-model="form.first_name" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="First Name"
                               required
                        />
                    </div>
                    <div>
                        <input v-model="form.last_name" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Last Name"
                               required
                        />
                    </div>
                    <div>
                        <input v-model="form.phone_number" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Phone Number"
                               required
                        />
                    </div>
                    <div>
                        <select v-model="form.language" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                required
                        >
                            <option value="en">English</option>
                            <option value="es">Spanish</option>
                        </select>
                    </div>
                </div>

                <div>
                    <button :disabled="loading" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            type="submit"
                    >
                        Send Verification Code
                    </button>
                </div>
            </form>

            <form v-if="consentId && status === 'pending'" class="mt-8 space-y-6" @submit.prevent="verifyCode">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <input v-model="verificationCode" class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Verification Code"
                               required
                        />
                    </div>
                </div>

                <div>
                    <button :disabled="loading" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            type="submit"
                    >
                        Verify Code
                    </button>
                </div>
            </form>

            <div v-if="status" :class="statusClass" class="mt-4 text-center text-sm font-medium">
                {{ statusMessage }}
            </div>

            <div v-if="errorMessage" class="mt-4 text-center text-sm text-red-600">
                {{ errorMessage }}
            </div>
        </div>
    </div>
</template>

<script setup>
import axios                                             from 'axios';
import {computed, onMounted, onUnmounted, reactive, ref} from 'vue';
import {useRoute, useRouter}                             from 'vue-router';
import echo                                              from '../echo.js';

const router = useRouter();
const route = useRoute();
const companyHash = route.params.companyHash;

const form = reactive({
    first_name: '',
    last_name: '',
    phone_number: '',
    language: 'en',
});
const verificationCode = ref('');
const consentId = ref(null);
const status = ref(null);
const errorMessage = ref(null);
const loading = ref(false);
const company = ref(null);

const formTitle = computed(() => {
    if (!consentId.value) {
        return 'Consent Form';
    }
    if (status.value === 'pending') {
        return 'Verify Code';
    }
    return 'Consent Status';
});

const statusMessage = computed(() => {
    switch (status.value) {
        case 'pending':
            return 'Waiting for verification code';
        case 'awaiting_consent':
            return 'Waiting for consent';
        case 'consent_confirmed':
            return 'Consent confirmed';
        case 'consent_rejected':
            return 'Consent rejected';
        default:
            return 'Unknown status';
    }
});

const statusClass = computed(() => {
    switch (status.value) {
        case 'consent_confirmed':
            return 'text-green-600';
        case 'consent_rejected':
            return 'text-red-600';
        default:
            return 'text-gray-600';
    }
});

const listenForStatusUpdates = () => {
    if (consentId.value) {
        echo.private(`consent.${ consentId.value }`)
            .listen('ConsentStatusUpdated', (e) => {
                status.value = e.status;
            });
    }
};

const stopListeningForStatusUpdates = () => {
    if (consentId.value) {
        echo.leave(`consent.${ consentId.value }`);
    }
};

const fetchCompanyDetails = async () => {
    try {
        const response = await axios.get(`/api/companies/${ companyHash }`);
        company.value = response.data;
    } catch (error) {
        console.error('Failed to fetch company details:', error);
        errorMessage.value = 'Failed to load company details';
    }
};

onMounted(() => {
    fetchCompanyDetails();
    if (consentId.value) {
        listenForStatusUpdates();
    }
});

onUnmounted(() => {
    stopListeningForStatusUpdates();
});

const submitForm = async () => {
    loading.value = true;
    try {
        const response = await axios.post('/api/consents', {
            ...form,
            company_hash: companyHash,
        });
        consentId.value = response.data.data.id;
        status.value = 'pending';
        errorMessage.value = null;
        listenForStatusUpdates();
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'An error occurred';
    } finally {
        loading.value = false;
    }
};

const verifyCode = async () => {
    loading.value = true;
    try {
        const response = await axios.post(`/api/consents/${ consentId.value }/verify`, {
            code: verificationCode.value,
        });
        if (response.data.success) {
            status.value = 'awaiting_consent';
        } else {
            errorMessage.value = 'Invalid verification code';
        }
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'An error occurred';
    } finally {
        loading.value = false;
    }
};

const goBack = () => {
    router.push({name: 'Companies'})
}
</script>
