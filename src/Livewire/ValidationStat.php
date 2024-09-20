<?php

namespace Xbigdaddyx\Beverly\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class ValidationStat extends Component
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
        if (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX') {
            return view('beverly::livewire.carton-box.validation-stat', ['count' => $this->polybags->count(), 'tags_count' => $this->tags->count()]);
        }
        return view('beverly::livewire.carton-box.validation-stat', ['count' => $this->polybags->count()]);
    }
}
