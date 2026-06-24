<?php

use App\Models\Profile;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] class extends Component
{
    use WithFileUploads;

    public bool $embedded = false;
    public Profile $profile;
    public $photo = null;
    public string $name = '';
    public ?int $age = null;
    public ?string $bio = null;
    public string $gender = 'Male';
    public string $looking_for = 'Everyone';
    public bool $saved = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->profile = $user->profile()->firstOrCreate([], ['gender' => 'Male', 'looking_for' => 'Everyone']);
        $this->name = $user->name;
        $this->age = $this->profile->age;
        $this->bio = $this->profile->bio;
        $this->gender = $this->profile->gender;
        $this->looking_for = $this->profile->looking_for;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:18', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'looking_for' => ['required', Rule::in(['Male', 'Female', 'Everyone'])],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();
        $user->update(['name' => $validated['name']]);
        $attributes = ['age' => $validated['age'], 'bio' => $validated['bio'], 'gender' => $validated['gender'], 'looking_for' => $validated['looking_for']];
        if ($this->photo) {
            $attributes['photo_path'] = $this->photo->store('profile-photos', 'public');
        }

        $this->profile = $user->profile()->updateOrCreate([], $attributes);
        $this->photo = null;
        $this->saved = true;
    }
}; ?>

<div class="max-w-7xl  mx-auto px-4 py-10 sm:px-6 lg:px-8">
    <div class="rounded-xl bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">Edit Dating Profile</h1><p class="mt-1 text-sm text-gray-600">Tell people a little more about you.</p>
        <form wire:submit="save" class="mt-6 space-y-5">
            <div><x-input-label for="photo" value="Profile photo" /><div class="mt-2 flex items-center gap-4"><img src="{{ $photo ? $photo->temporaryUrl() : $profile->avatar_url }}" class="h-20 w-20 rounded-full object-cover" alt="Current avatar"><input wire:model="photo" id="photo" type="file" accept=".jpg,.jpeg,.png,.webp" class="block w-full text-sm text-gray-600"></div><x-input-error :messages="$errors->get('photo')" class="mt-2" /></div>
            <div><x-input-label for="name" value="Name" /><x-text-input wire:model="name" id="name" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
            <div><x-input-label for="age" value="Age" /><x-text-input wire:model="age" id="age" type="number" class="mt-1 block w-full" /><x-input-error :messages="$errors->get('age')" class="mt-2" /></div>
            <div><x-input-label for="bio" value="Bio" /><textarea wire:model="bio" id="bio" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea><x-input-error :messages="$errors->get('bio')" class="mt-2" /></div>
            <div><x-input-label for="gender" value="Gender" /><select wire:model="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300"><option>Male</option><option>Female</option></select><x-input-error :messages="$errors->get('gender')" class="mt-2" /></div>
            <div><x-input-label for="looking_for" value="Looking For" /><select wire:model="looking_for" id="looking_for" class="mt-1 block w-full rounded-md border-gray-300"><option>Male</option><option>Female</option><option>Everyone</option></select><x-input-error :messages="$errors->get('looking_for')" class="mt-2" /></div>
            <x-primary-button>Save Dating Profile</x-primary-button>
            @if ($saved)<div class="rounded-md bg-green-50 p-3 text-sm text-green-700">Your dating profile has been saved.</div>@endif
        </form>
    </div>
</div>
