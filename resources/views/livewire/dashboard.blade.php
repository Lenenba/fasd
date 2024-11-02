<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\ListeningParty;
use App\Models\Episode;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required')]
    public $startTime;

    #[Validate('required|url')]
    public string $mediaUrl = '';

    public function createListeningParty()
    {
        $this->validate();
        $episode = Episode::create([
            'media_url' => $this->mediaUrl,
        ]);

        $listeningParty = ListeningParty::create([
            'episode_id' => $episode->id,
            'name' => $this->name,
            'start_time' => $this->startTime,
        ]);

        return redirect()->route('parties.show', $listeningParty);
    }
    public function with()
    {
        return [
            'listening_parties' => listeningParty::all(),
        ];
    }
}; ?>

<div class="flex items-center justify-center min-h-screen bg-slate-50">
    <div class="max-w-lg w-full px-4">
        <form wire:submit='createListeningParty' class="space-y-6">
            <x-input wire:model='name' placeholder="Listening Party Name" />
            <x-input wire:model='mediaUrl' placeholder="Podcast RSS Feed URL"
                description="Entering de RSS Feed will grab the latest episode" />
            <x-datetime-picker wire:model='startTime' placeholder="Listening PartyStart time" :min="now()->subDays(1)" />
            <x-button primary type="submit"> Create Listening Party</x-button>
        </form>
    </div>
</div>
