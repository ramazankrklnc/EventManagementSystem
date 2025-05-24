<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Hesabı Sil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Hesabınız silindiğinde, tüm kaynakları ve verileri kalıcı olarak silinecektir. Hesabınızı silmeden önce, saklamak istediğiniz verileri indirin.
        </p>

        @if($user->isAdmin())
            <div class="mt-2 p-4 bg-red-100 rounded-lg">
                <p class="text-red-700">
                    <strong>Yönetici Uyarısı:</strong> Yönetici hesabınızı silmek, yönettiğiniz içerikleri etkileyebilir. Lütfen bu işlemi yapmadan önce başka bir yönetici atadığınızdan emin olun.
                </p>
            </div>
        @endif
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Hesabı Sil</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Hesabınızı silmek istediğinizden emin misiniz?
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Hesabınız silindiğinde, tüm kaynakları ve verileri kalıcı olarak silinecektir. Hesabınızı kalıcı olarak silmek istediğinizi onaylamak için lütfen şifrenizi girin.
            </p>

            <div class="mt-4 p-4 bg-yellow-100 rounded-lg">
                <p class="text-yellow-700">
                    <strong>Önemli Uyarı:</strong> Bu işlem geri alınamaz. Hesabınız ve tüm verileriniz kalıcı olarak silinecektir.
                </p>
            </div>

            <div class="mt-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="confirm_deletion" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" required>
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Hesabımı kalıcı olarak silmek istediğimi onaylıyorum.</span>
                </label>
            </div>

            <div class="mt-6">
                <x-input-label for="password" value="Şifre" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Şifre"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    İptal
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Hesabı Sil
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
