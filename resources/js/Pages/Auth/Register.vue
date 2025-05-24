<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

//  Register (Kayıt Ol) sayfası Vue dosyası. Kullanıcıdan ad-soyad, e-posta, şifre ve 
// şifre tekrar bilgilerini alarak yeni kullanıcı kaydı oluşturur.
// Form validasyonu, hata mesajları ve kayıt ol butonu gibi ana bölümleri içerir.
// Ayrıca, kullanıcı zaten hesabı varsa giriş yap bağlantısı da sunulur.

const loading = ref(false);
const errorMessage = ref('');

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        await form.post(route('register'), {
            onFinish: () => {
                form.reset('password', 'password_confirmation');
                loading.value = false;
            },
            onError: (errors) => {
                loading.value = false;
                if (errors.name) {
                    errorMessage.value = errors.name;
                } else if (errors.email) {
                    errorMessage.value = errors.email;
                } else if (errors.password) {
                    errorMessage.value = errors.password;
                } else {
                    errorMessage.value = 'Kayıt olurken bir hata oluştu.';
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
        <Head title="Kayıt Ol" />

        <div class="flex min-h-full flex-col justify-center">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <!-- Logo -->
                <div class="flex justify-center">
                    <Link href="/" class="text-4xl font-bold text-green-500 hover:text-green-400 transition duration-150 ease-in-out">
                        EYS
                    </Link>
                </div>
                
                <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-white">
                    Yeni Hesap Oluştur
                </h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <div v-if="errorMessage" class="mb-4 p-4 rounded-md bg-red-500/10 border border-red-500">
                    <p class="text-sm text-red-500">{{ errorMessage }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Ad Soyad" class="text-gray-200" />
                        <div class="mt-2">
                            <TextInput
                                id="name"
                                type="text"
                                class="block w-full rounded-md border-0 bg-gray-700/50 py-2 px-3 text-white shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm sm:leading-6"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                :disabled="loading"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="email" value="E-posta Adresi" class="text-gray-200" />
                        <div class="mt-2">
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full rounded-md border-0 bg-gray-700/50 py-2 px-3 text-white shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm sm:leading-6"
                                v-model="form.email"
                                required
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
                                autocomplete="new-password"
                                :disabled="loading"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Şifre Tekrar" class="text-gray-200" />
                        <div class="mt-2">
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="block w-full rounded-md border-0 bg-gray-700/50 py-2 px-3 text-white shadow-sm ring-1 ring-inset ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-500 sm:text-sm sm:leading-6"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                :disabled="loading"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <div>
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing || loading }"
                            :disabled="form.processing || loading"
                            class="flex w-full justify-center rounded-md bg-green-500 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-500"
                        >
                            <span v-if="loading">Kayıt Yapılıyor...</span>
                            <span v-else>Kayıt Ol</span>
                        </PrimaryButton>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-gray-400">
                    Zaten hesabınız var mı?
                    <a href="/login" class="font-semibold leading-6 text-green-500 hover:text-green-400">
                        Giriş Yap
                    </a>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>
