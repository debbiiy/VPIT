<x-guest-layout>
    <h1 class="text-2xl font-bold text-center mb-2"><span class="text-blue-600">Vendor</span><span class="text-gray-800">Portal</span></h1>
    <p class="text-sm text-center text-gray-600 mb-6">Create your account</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <x-text-input id="name" class="block w-full bg-blue-50" type="text" name="name" :value="old('name')" placeholder="Full Name" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <x-text-input id="email" class="block w-full bg-blue-50" type="email" name="email" :value="old('email')" placeholder="Email Address" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mb-4">
            <select id="role" name="role" required class="block w-full bg-blue-50 rounded-md border-gray-300">
                <option value="">-- Register as --</option>
                <option value="user">User</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-text-input id="password" class="block w-full bg-blue-50" type="password" name="password" placeholder="Password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <x-text-input id="password_confirmation" class="block w-full bg-blue-50" type="password" name="password_confirmation" placeholder="Confirm Password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Register
        </x-primary-button>

        <p class="text-center text-sm text-gray-700 mt-4">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login here</a>
        </p>
    </form>
</x-guest-layout>