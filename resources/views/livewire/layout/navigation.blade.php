<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-rose-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-6"><a href="{{ route('browse') }}" class="flex items-center gap-2 font-bold text-rose-600" wire:navigate><img src="{{ asset('heart.png') }}" class="w-8 h-8 rounded-full" alt="my date">my date</a><div class="hidden sm:flex gap-5 text-sm"><a href="{{ route('browse') }}" wire:navigate>Browse Profiles</a><a href="{{ route('messages') }}" wire:navigate>Messages</a><a href="{{ route('profile.edit') }}" wire:navigate>Edit Dating Profile</a></div></div>
        @if ($user = auth()->user())
            <div class="hidden sm:flex items-center gap-3"><x-dropdown align="right" width="48"><x-slot name="trigger"><button class="text-sm text-gray-600">{{ $user->name }}</button></x-slot><x-slot name="content"><x-dropdown-link :href="route('profile')" wire:navigate>Account</x-dropdown-link><button wire:click="logout" class="w-full text-start"><x-dropdown-link>Log Out</x-dropdown-link></button></x-slot></x-dropdown></div>
        @endif
        <button @click="open = ! open" class="sm:hidden p-2 text-gray-500">Menu</button>
    </div>
    <div x-show="open" class="sm:hidden px-4 pb-4 space-y-2"><a class="block" href="{{ route('browse') }}" wire:navigate>Browse Profiles</a><a class="block" href="{{ route('messages') }}" wire:navigate>Messages</a><a class="block" href="{{ route('profile.edit') }}" wire:navigate>Edit Dating Profile</a>@if ($user)<button wire:click="logout" class="block">Log Out</button>@endif</div>
</nav>
