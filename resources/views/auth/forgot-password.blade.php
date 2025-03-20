<x-guest-layout>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow-md border border-blue-200">
        <h2 class="text-2xl font-bold text-center text-blue-900">Mot de passe oublié ?</h2>
        <p class="text-sm text-gray-600 text-center mt-2">
            Pas de souci ! Saisissez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="mt-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-blue-900 font-semibold" />
                <x-text-input id="email" class="block mt-1 w-full border border-blue-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 bg-blue-50"
                              type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
            </div>

            <div class="flex items-center justify-center mt-6">
                <x-primary-button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md">
                    {{ __('Envoyer le lien de réinitialisation') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
