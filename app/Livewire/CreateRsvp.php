<?php

namespace App\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;

class CreateRsvp extends Component
{
    public string $name = '';
    public string $attendance = '';
    public string $amount = '1';

    #[Locked]
    public bool $submitted = false;

    public function submit(): void
    {
        // Reject submissions from invitation pages opened before the form was removed.
        abort(403, 'Invitation responses are closed.');
    }

    public function render()
    {
        return view('livewire.create-rsvp');
    }
}
