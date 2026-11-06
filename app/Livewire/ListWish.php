<?php

namespace App\Livewire;

use App\Models\Wishes;
use App\Models\Wedding;
use Livewire\Component;

class ListWish extends Component
{
    protected $listeners = ['wishCreated' => '$refresh'];

    public function render()
    {
        return view('livewire.list-wish', [
            'wish' => Wishes::where('wedding_id', Wedding::where('status', 'Active')->value('id'))->latest('id')->get()
        ]);
    }
}
