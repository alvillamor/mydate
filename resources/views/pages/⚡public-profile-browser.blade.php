<?php

use App\Models\Profile;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.public')] class extends Component
{
    use WithPagination;

    public string $gender = 'Everyone';

    public function updatedGender(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Profile::query()->with('user')->orderByDesc('created_at');
        if ($this->gender !== 'Everyone') {
            $query->where('gender', $this->gender);
        }

        return $this->view(['profiles' => $query->paginate(9)]);
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <div class="max-w-2xl"><p class="text-sm font-semibold uppercase tracking-wide text-rose-500">Meet someone lovely</p><h1 class="mt-2 text-4xl font-bold text-gray-900">Browse people on my date</h1><p class="mt-3 text-gray-600">A simple place to discover people and begin a conversation.</p></div>
    <div class="mt-6 flex flex-wrap gap-2"><button wire:click="$set('gender', 'Everyone')" class="rounded-full px-4 py-2 text-sm {{ $gender === 'Everyone' ? 'bg-rose-600 text-white' : 'bg-white text-gray-700' }}">Everyone</button><button wire:click="$set('gender', 'Male')" class="rounded-full px-4 py-2 text-sm {{ $gender === 'Male' ? 'bg-rose-600 text-white' : 'bg-white text-gray-700' }}">Male</button><button wire:click="$set('gender', 'Female')" class="rounded-full px-4 py-2 text-sm {{ $gender === 'Female' ? 'bg-rose-600 text-white' : 'bg-white text-gray-700' }}">Female</button></div>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($profiles as $profile)
            <article class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100"><img src="{{ $profile->avatar_url }}" alt="{{ $profile->user->name }}" class="h-64 w-full object-cover"><div class="p-5"><h2 class="text-xl font-semibold">{{ $profile->user->name }}@if ($profile->age), {{ $profile->age }}@endif</h2><p class="mt-1 text-sm text-rose-600">{{ $profile->gender }}</p><p class="mt-3 min-h-12 text-sm text-gray-600">{{ $profile->bio ?: 'Still writing their introduction.' }}</p><a href="{{ route('register') }}" class="mt-5 inline-flex rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500" wire:navigate>Sign Up to Message</a></div></article>
        @endforeach
    </div>
    @if ($profiles->hasPages())<div class="mt-8">{{ $profiles->links() }}</div>@endif
    @if ($profiles->isEmpty())<p class="mt-8 text-gray-500">No profiles match that filter yet.</p>@endif
</div>
