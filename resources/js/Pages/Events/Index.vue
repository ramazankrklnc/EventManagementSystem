<template>
    <Head title="Events" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Events
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtreler -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="flex flex-wrap gap-4">
                        <select v-model="selectedType" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" @change="filterEvents">
                            <option value="">Tüm Etkinlikler</option>
                            <option v-for="(label, value) in eventTypes" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <input type="date" v-model="selectedDate" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" @change="filterEvents">
                        <button @click="showRecommended" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                            Önerilen Etkinlikler
                        </button>
                    </div>
                </div>

                <!-- Etkinlik Listesi -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="event in events" :key="event.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="relative">
                            <img :src="event.image_url" :alt="event.title" class="w-full h-48 object-cover">
                            <div v-if="event.weather_dependent" class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-md text-sm">
                                Hava Durumuna Bağlı
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2 dark:text-gray-200">{{ event.title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ event.description }}</p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(event.event_date) }}</span>
                                <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-md">{{ event.type }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold dark:text-gray-200">{{ formatPrice(event.price) }} TL</span>
                                <button @click="addToCart(event)" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                                    Sepete Ekle
                                </button>
                            </div>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Kalan Bilet: {{ event.available_tickets }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sayfalama -->
                <div class="mt-6 flex justify-center" v-if="pagination">
                    <button 
                        v-for="page in pagination.last_page" 
                        :key="page"
                        @click="changePage(page)"
                        :class="[
                            'mx-1 px-4 py-2 rounded-md',
                            page === pagination.current_page 
                                ? 'bg-blue-500 text-white' 
                                : 'bg-white dark:bg-gray-800 text-blue-500 dark:text-blue-400'
                        ]"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const events = ref([]);
const eventTypes = ref({});
const pagination = ref(null);
const selectedType = ref('');
const selectedDate = ref('');

const fetchEvents = async (page = 1) => {
    try {
        const params = {
            page,
            type: selectedType.value,
            date: selectedDate.value
        };

        const response = await axios.get('/api/events', { params });
        events.value = response.data.data.data;
        pagination.value = response.data.data;
    } catch (error) {
        console.error('Error fetching events:', error);
    }
};

const fetchEventTypes = async () => {
    try {
        const response = await axios.get('/api/event-types');
        eventTypes.value = response.data.data;
    } catch (error) {
        console.error('Error fetching event types:', error);
    }
};

const filterEvents = () => {
    fetchEvents();
};

const showRecommended = async () => {
    try {
        const response = await axios.get('/api/events', {
            params: { recommended: true }
        });
        events.value = response.data.data.data;
        pagination.value = response.data.data;
    } catch (error) {
        console.error('Error fetching recommended events:', error);
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatPrice = (price) => {
    return Number(price).toLocaleString('tr-TR');
};

const addToCart = (event) => {
    // Sepete ekleme işlemi burada yapılacak
    console.log('Added to cart:', event);
};

const changePage = (page) => {
    fetchEvents(page);
};

onMounted(() => {
    fetchEventTypes();
    fetchEvents();
});
</script> 