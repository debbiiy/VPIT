<x-guest-layout>
    <h1 class="text-2xl font-bold text-center mb-2"><span class="text-blue-600">Vendor</span><span class="text-gray-800">Portal</span></h1>
    <p class="text-sm text-center text-gray-600 mb-6">Sign in to start your session</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <x-text-input id="email" class="block w-full bg-blue-50" type="email" name="email" :value="old('email')" placeholder="Email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-text-input id="password" class="block w-full bg-blue-50" type="password" name="password" placeholder="Password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex justify-between items-center mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="form-checkbox" />
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Forgot your password?</a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center">
            Sign In
        </x-primary-button>

        <p class="text-center text-sm text-gray-700 mt-4">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register now</a>
        </p>
    </form>
</x-guest-layout>