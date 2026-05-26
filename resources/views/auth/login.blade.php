<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('error'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        <p class="font-semibold">Demo akun login</p>
        <p class="mt-1">Gunakan akun berikut untuk mencoba login:</p>

        <div class="mt-3 space-y-3">
            <div>
                <p class="font-medium">Admin</p>
                <div class="mt-1 flex gap-2">
                    <input id="admin-email" type="text" readonly value="admin@demo.com" class="w-full rounded border border-amber-200 bg-white px-3 py-2 text-sm">
                    <button type="button" onclick="copyToClipboard('admin-email')" class="rounded bg-amber-600 px-3 py-2 text-xs font-semibold text-white">Copy</button>
                </div>
                <div class="mt-2 flex gap-2">
                    <input id="admin-password" type="text" readonly value="Admin123!" class="w-full rounded border border-amber-200 bg-white px-3 py-2 text-sm">
                    <button type="button" onclick="copyToClipboard('admin-password')" class="rounded bg-amber-600 px-3 py-2 text-xs font-semibold text-white">Copy</button>
                </div>
            </div>

            <div>
                <p class="font-medium">User</p>
                <div class="mt-1 flex gap-2">
                    <input id="user-email" type="text" readonly value="user@demo.com" class="w-full rounded border border-amber-200 bg-white px-3 py-2 text-sm">
                    <button type="button" onclick="copyToClipboard('user-email')" class="rounded bg-amber-600 px-3 py-2 text-xs font-semibold text-white">Copy</button>
                </div>
                <div class="mt-2 flex gap-2">
                    <input id="user-password" type="text" readonly value="User123!" class="w-full rounded border border-amber-200 bg-white px-3 py-2 text-sm">
                    <button type="button" onclick="copyToClipboard('user-password')" class="rounded bg-amber-600 px-3 py-2 text-xs font-semibold text-white">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(inputId) {
            const input = document.getElementById(inputId);
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value);

            const button = input.nextElementSibling;
            const originalText = button.textContent;
            button.textContent = 'Copied';
            setTimeout(() => {
                button.textContent = originalText;
            }, 1500);
        }
    </script>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>