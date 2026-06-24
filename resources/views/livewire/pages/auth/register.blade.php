<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);
        $validated['password'] = Hash::make($validated['password']);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create($validated);
            Profile::create([
                'user_id' => $user->id,
                'gender' => 'Male',
                'looking_for' => 'Everyone',
                'age' => null,
                'bio' => null,
                'photo_path' => null,
                'photo_url' => null,
            ]);
            return $user;
        });

        event(new Registered($user));
        Auth::login($user);
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <div><x-input-label for="name" :value="__('Name')" /><x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required autofocus autocomplete="name" /><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
        <div class="mt-4"><x-input-label for="email" :value="__('Email')" /><x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
        <div class="mt-4"><x-input-label for="password" :value="__('Password')" /><x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
        <div class="mt-4"><x-input-label for="password_confirmation" :value="__('Confirm Password')" /><x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" /></div>
        <div class="flex items-center justify-end mt-4"><a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" wire:navigate>Already registered?</a><x-primary-button class="ms-4">Register</x-primary-button></div>
    </form>
</div>
