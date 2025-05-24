<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-900">
        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <slot />
        </div>
    </div>
</template>
