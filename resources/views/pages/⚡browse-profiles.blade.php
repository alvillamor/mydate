<?php

use App\Models\Profile;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    public string $gender = 'Everyone';

    public function mount(): void
    {
        $lookingFor = auth()->user()?->profile?->looking_for;

        if (in_array($lookingFor, ['Male', 'Female', 'Everyone'], true)) {
            $this->gender = $lookingFor;
        }
    }

    public function updatedGender(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Profile::query()->with('user')->where('user_id', '!=', auth()->id())->orderByDesc('created_at');
        if ($this->gender !== 'Everyone') {
            $query->where('gender', $this->gender);
        }

        $profileCount = $query->count();
        $profiles = $query->paginate(9);

        return $this->view(['profiles' => $profiles, 'profileCount' => $profileCount]);
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <div><p class="text-sm font-semibold uppercase tracking-wide text-rose-500">Browse profiles</p><h1 class="mt-2 text-3xl font-bold">Find your next conversation</h1></div>
    <div class="mt-6 flex flex-wrap items-center gap-2"><button wire:click="$set('gender', 'Everyone')" class="rounded-full px-4 py-2 text-sm {{ $gender === 'Everyone' ? 'bg-rose-600 text-white' : 'bg-white' }}">Everyone</button><button wire:click="$set('gender', 'Male')" class="rounded-full px-4 py-2 text-sm {{ $gender === 'Male' ? 'bg-rose-600 text-white' : 'bg-white' }}">Male</button><button wire:click="$set('gender', 'Female')" class="rounded-full px-4 py-2 text-sm {{ $gender === 'Female' ? 'bg-rose-600 text-white' : 'bg-white' }}">Female</button><span class="ml-auto text-sm text-gray-500">{{ $profileCount }} profiles</span></div>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($profiles as $profile)
            <article class="overflow-hidden rounded-xl bg-white shadow-sm"><img src="{{ $profile->avatar_url }}" alt="{{ $profile->user->name }}" class="h-64 w-full object-cover"><div class="p-5"><h2 class="text-xl font-semibold">{{ $profile->user->name }}@if ($profile->age), {{ $profile->age }}@endif</h2><p class="mt-1 text-sm text-rose-600">{{ $profile->gender }} &middot; Looking for {{ $profile->looking_for }}</p><p class="mt-3 min-h-12 text-sm text-gray-600">{{ $profile->bio ?: 'Still writing their introduction.' }}</p><a href="{{ route('chat.show', $profile->user) }}" class="mt-5 inline-flex rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500" wire:navigate>Message</a></div></article>
        @endforeach
    </div>
    @if ($profiles->hasPages())<div class="mt-8">{{ $profiles->links() }}</div>@endif
    @if ($profiles->isEmpty())<p class="mt-8 text-gray-500">No profiles match that filter yet.</p>@endif
</div>
