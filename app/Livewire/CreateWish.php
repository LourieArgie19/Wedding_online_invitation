<?php

namespace App\Livewire;

use Livewire\Component;

class CreateWish extends Component
{
    public $name;
    public $comment;
    public bool $submitted = false;

    public function render()
    {
        return view('livewire.create-wish');
    }

    public function createWish(): void
    {
        // Reject submissions from invitation pages opened before the form was removed.
        abort(403, 'Wedding wishes are read-only.');
    }
}
