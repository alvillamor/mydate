<?php

use App\Models\Message;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public User $recipient;
    public string $body = '';
    public ?int $latestMessageId = null;

    public function mount(User $user): void
    {
        if ($user->is(auth()->user())) {
            abort(403);
        }

        $this->recipient = $user->load('profile');
        Message::where('sender_id', $user->id)->where('receiver_id', auth()->id())->whereNull('read_at')->update(['read_at' => now()]);
        $this->latestMessageId = $this->latestConversationMessageId();
    }

    public function send(): void
    {
        $validated = $this->validate(['body' => ['required', 'string', 'max:1000']]);
        $message = Message::create(['sender_id' => auth()->id(), 'receiver_id' => $this->recipient->id, 'body' => $validated['body']]);
        $this->latestMessageId = $message->id;
        $this->reset('body');
        $this->dispatch('chat-updated');
    }

    public function refreshMessages(): void
    {
        Message::where('sender_id', $this->recipient->id)->where('receiver_id', auth()->id())->whereNull('read_at')->update(['read_at' => now()]);
        $latestMessageId = $this->latestConversationMessageId();

        if ($latestMessageId !== $this->latestMessageId) {
            $this->latestMessageId = $latestMessageId;
            $this->dispatch('chat-updated');
        }
    }

    private function latestConversationMessageId(): ?int
    {
        $latestMessageId = Message::query()
            ->where(function ($query) { $query->where('sender_id', auth()->id())->where('receiver_id', $this->recipient->id); })
            ->orWhere(function ($query) { $query->where('sender_id', $this->recipient->id)->where('receiver_id', auth()->id()); })
            ->max('id');

        return $latestMessageId === null ? null : (int) $latestMessageId;
    }

    public function render()
    {
        $messages = Message::query()
            ->where(function ($query) { $query->where('sender_id', auth()->id())->where('receiver_id', $this->recipient->id); })
            ->orWhere(function ($query) { $query->where('sender_id', $this->recipient->id)->where('receiver_id', auth()->id()); })
            ->orderBy('created_at')
            ->get();

        return $this->view(['messages' => $messages]);
    }
}; ?>

<div class="max-w-7xl  mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <div class=" rounded-xl bg-white shadow-sm"><div class="flex items-center gap-3 border-b p-4"><img src="{{ $recipient->profile?->avatar_url ?? asset('images/placeholders/male.png') }}" class="h-11 w-11 rounded-full object-cover" alt="{{ $recipient->name }}"><div><h1 class="font-semibold">{{ $recipient->name }}</h1></div></div>
        <div
            wire:poll.5s="refreshMessages"
            x-data
            x-init="
                const scrollToLatest = () => $nextTick(() => $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' }));
                scrollToLatest();
                $wire.on('chat-updated', scrollToLatest);
            "
            class="min-h-96 space-y-3 bg-gray-50 p-5 h-[calc(100vh-280px)] overflow-auto"
        >
            @foreach ($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}"><div class="max-w-xs rounded-2xl shadow px-4 py-3 text-sm {{ $message->sender_id === auth()->id() ? 'bg-rose-600 text-white' : 'bg-white text-gray-800' }}"><p>{{ $message->body }}</p><p class="mt-1 text-xs opacity-70">{{ $message->created_at->format('M j, g:i a') }}</p></div></div>
            @endforeach
        </div>
        <form wire:submit="send" class="border-t p-4"><div class="flex gap-3"><x-text-input wire:model="body" class="block w-full" placeholder="Write a message?" /><x-primary-button>Send</x-primary-button></div><x-input-error :messages="$errors->get('body')" class="mt-2" /></form>
    </div>
</div>
