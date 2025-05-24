<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            Şifre Güncelleme
        </h2>

        <p class="mt-1 text-sm dark:text-white">
            Güvenliğiniz için güçlü bir şifre kullanın.
        </p>

        @if(!$user->password_changed)
            <div class="mt-2 p-4 bg-yellow-100 rounded-lg">
                <p class="text-yellow-700">
                    <strong>İlk Giriş Uyarısı:</strong> Güvenliğiniz için ilk girişte şifrenizi değiştirmeniz gerekmektedir.
                </p>
            </div>
        @endif
    </header>

    <form method="post" action="{{ route('profile.password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" value="Mevcut Şifre" class="dark:text-white" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Yeni Şifre" class="dark:text-white" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Yeni Şifre (Tekrar)" class="dark:text-white" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="dark:text-white">
            Şifreniz aşağıdaki kriterleri karşılamalıdır:
            <ul class="list-disc list-inside mt-2 space-y-1 dark:text-white">
                <li>En az 8 karakter uzunluğunda</li>
                <li>En az bir büyük harf</li>
                <li>En az bir küçük harf</li>
                <li>En az bir rakam</li>
                <li>En az bir özel karakter</li>
            </ul>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Şifreyi Güncelle</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm dark:text-white"
                >Şifre güncellendi.</p>
            @endif
        </div>
    </form>
</section>
