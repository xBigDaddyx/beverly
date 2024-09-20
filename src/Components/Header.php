<?php

namespace Xbigdaddyx\Beverly\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Header extends Component
{
   public function __construct(
    public string $title = '',
    public string $parentLevel = '',
    public string $secondLevel = '',
    public string $style = 'default',
   )
   {
    //
   }
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('beverly::components.header');
    }
}
