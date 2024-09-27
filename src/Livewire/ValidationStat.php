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
        if ($this->carton->type === 'RATIO' || $this->carton->type === 'MIX') {
            return view('beverly::livewire.carton-box.validation-stat', ['count' => $this->carton->solidPolybags->count(), 'tags_count' => $this->tags->count()]);
        }
        return view('beverly::livewire.carton-box.validation-stat', ['count' => $this->carton->solidPolybags->count()]);
    }
}
