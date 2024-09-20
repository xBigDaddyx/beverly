<?php

namespace Xbigdaddyx\Beverly\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CheckBoxForm extends Form
{
    #[Validate('required')]
    public $box_code = '';
    public $po;
    public $carton_number;
}
