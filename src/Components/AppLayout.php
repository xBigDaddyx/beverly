<?php

namespace Xbigdaddyx\Beverly\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('beverly::layouts.app');
    }
}
