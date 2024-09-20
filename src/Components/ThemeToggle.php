<?php

namespace Xbigdaddyx\Beverly\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ThemeToggle extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('beverly::components.theme-toggle');
    }
}
