<?php

namespace Xbigdaddyx\Beverly\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ValidationAttribute extends Component
{

    public $carton;
    #[Reactive]
    public $polybags;
    #[Reactive]
    public $type;
    #[Reactive]
    public $tags;

    public function render()
    {
        return view('beverly::livewire.carton-box.validation-attribute');
    }
}
