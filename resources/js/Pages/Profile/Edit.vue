// Türkçe Açıklama: Profilim (Profile) sayfasının ana Vue dosyası. Kullanıcı profil bilgilerini, şifresini güncelleyebilir ve hesabını silebilir.
// Sayfa, üç ana form bileşenini içerir: Profil Bilgileri Güncelleme, Şifre Güncelleme ve Hesap Silme.
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Giriş yapmış kullanıcılar için ana layout
import DeleteUserForm from './Partials/DeleteUserForm.vue'; // Hesap silme formu
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'; // Şifre güncelleme formu
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'; // Profil bilgileri güncelleme formu
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <!-- Sayfa başlığı ayarlanıyor -->
    <Head title="Profile" />

    <!-- Giriş yapmış kullanıcılar için ana layout -->
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Profil Bilgileri Güncelleme Formu -->
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Şifre Güncelleme Formu -->
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Hesap Silme Formu -->
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
