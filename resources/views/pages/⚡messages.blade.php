<?php

use App\Models\Message;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function getConversationsProperty(): array
    {
        $messages = Message::query()->with(['sender.profile', 'receiver.profile'])
            ->where(function ($query) { $query->where('sender_id', auth()->id())->orWhere('receiver_id', auth()->id()); })
            ->orderByDesc('created_at')->get();
        $conversations = [];

        foreach ($messages as $message) {
            $other = $message->sender_id === auth()->id() ? $message->receiver : $message->sender;
            if (! isset($conversations[$other->id])) {
                $conversations[$other->id] = ['user' => $other, 'message' => $message, 'unread' => 0];
            }
            if ($message->receiver_id === auth()->id() && $message->read_at === null) {
                $conversations[$other->id]['unread']++;
            }
        }

        return $conversations;
    }

    public function render()
    {
        return $this->view();
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8"><div class="rounded-xl bg-white shadow-sm"><div class="border-b p-6"><h1 class="text-2xl font-bold">Messages</h1><p class="mt-1 text-sm text-gray-600">Your conversations, all in one place.</p></div>
    <div wire:poll.10s class="divide-y divide-gray-100">
        @foreach ($this->conversations as $conversation)
            <a href="{{ route('chat.show', $conversation['user']) }}" wire:navigate class="flex items-center gap-4 p-5 hover:bg-gray-50"><img src="{{ $conversation['user']->profile?->avatar_url ?? asset('images/placeholders/male.png') }}" class="h-14 w-14 rounded-full object-cover" alt="{{ $conversation['user']->name }}"><div class="min-w-0 flex-1"><div class="flex items-center justify-between gap-3"><p class="font-semibold">{{ $conversation['user']->name }}</p><time class="text-xs text-gray-500">{{ $conversation['message']->created_at->format('M j') }}</time></div><p class="truncate text-sm text-gray-600">{{ $conversation['message']->body }}</p></div>@if ($conversation['unread'])<span class="rounded-full bg-rose-600 px-2 py-1 text-xs font-semibold text-white">{{ $conversation['unread'] }}</span>@endif</a>
        @endforeach
        @if (count($this->conversations) === 0)<p class="p-6 text-sm text-gray-500">No conversations yet. Browse profiles to say hello.</p>@endif
    </div>
</div></div>
