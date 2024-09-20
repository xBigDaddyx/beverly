<?php

namespace Xbigdaddyx\Beverly\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;

class ValidationBoxForm extends Form
{
    #[Rule('required')]
    public $tag_barcode = '';
    #[Rule('required')]
    public $polybag_barcode = '';
    public $additional;
}
