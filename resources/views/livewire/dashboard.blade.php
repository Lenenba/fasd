<?php

use Livewire\Volt\Component;
use App\Models\ListeningParty;

new class extends Component {
    public string $name = '';
    public $startTime;

    public function createListeningParty()
    {

    }
    public function with()
    {
        return [
            'listening_parties' => listeningParty::all(),
    ];
    }
}; ?>

<div>
    Hello word.
</div>
