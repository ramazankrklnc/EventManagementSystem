<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

//  Login (Giriş) sayfası Vue dosyası. Kullanıcıdan e-posta ve şifre alarak giriş işlemini başlatır.
// Form validasyonu, hata mesajları ve giriş butonu gibi ana bölümleri içerir.
// Ayrıca, kullanıcı şifresini unuttuysa şifre sıfırlama bağlantısı da sunulabilir.

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const loading = ref(false);
const errorMessage = ref('');

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        await form.post(route('login'), {
            onFinish: () => {
                form.reset('password');
                loading.value = false;
            },
            onError: (errors) => {
                loading.value = false;
                if (errors.email) {
                    errorMessage.value = errors.email;
                } else if (errors.password) {
                    errorMessage.value = errors.password;
                } else {
                    errorMessage.value = 'Giriş yapılırken bir hata oluştu.';
                }
            },
        });
    } catch (error) {
        loading.value = false;
        errorMessage.value = 'Sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.';
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Giriş Yap" />

        <div class="flex min-h-full flex-col justify-center">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <!-- Logo -->
                <div class="flex justify-center">
                    <Link href="/" class="text-4xl font-bold text-green-500 hover:text-green-400 transition duration-150 ease-in-out">
                        EYS
                    </Link>
                </div>
                
                <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-white">
                    Hesabınıza Giriş Yapın
                </h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <div v-if="errorMessage" class="mb-4 p-4 rounded-md bg-red-500/10 border border-red-500">
                    <p class="text-sm text-red-500">{{ errorMessage }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="E-posta Adresi" class="text-gray-200" />
                        <div class="mt-2">
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full rounded-md border-0 bg-gray-700/50 py-2 px-3 text-white shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm sm:leading-6"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                :disabled="loading"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="password" value="Şifre" class="text-gray-200" />
                        <div class="mt-2">
                            <TextInput
                                id="password"
                                type="password"
                                class="block w-full rounded-md border-0 bg-gray-700/50 py-2 px-3 text-white shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm sm:leading-6"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                :disabled="loading"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>
                    </div>

                    <div class="flex items-center">
                        <Checkbox
                            name="remember"
                            v-model:checked="form.remember"
                            :disabled="loading"
                            class="h-4 w-4 rounded border-gray-600 bg-gray-700/50 text-green-500 focus:ring-green-500 focus:ring-offset-gray-900"
                        />
                        <span class="ml-3 block text-sm leading-6 text-gray-200">Beni Hatırla</span>
                    </div>

                    <div>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing || loading }"
                            :disabled="form.processing || loading"
                            class="flex w-full justify-center rounded-md bg-green-500 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500"
                        >
                            <span v-if="loading">Giriş Yapılıyor...</span>
                            <span v-else>Giriş Yap</span>
                        </PrimaryButton>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-gray-400">
                    Hesabınız yok mu?
                    <a href="/register" class="font-semibold leading-6 text-green-500 hover:text-green-400">
                        Kayıt Ol
                    </a>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>
