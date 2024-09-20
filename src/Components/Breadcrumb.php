<?php

namespace Xbigdaddyx\Beverly\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Breadcrumb extends Component
{
   public function __construct(
    public string $parentLevel = '',
    public string $secondLevel = '',
    public string $iconParent = 'file',
    public string $iconSecond = 'file',
   )
   {
    //
   }
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('beverly::components.breadcrumb');
    }
}
