<?php

namespace Xbigdaddyx\Beverly\Livewire;


use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use Xbigdaddyx\Beverly\Livewire\Forms\CheckBoxForm;
use Xbigdaddyx\Beverly\Models\CartonBox;
use Xbigdaddyx\Beverly\Models\PackingList;
use WireUi\Traits\WireUiActions;
use Masmerise\Toaster\Toaster;
use Xbigdaddyx\Beverly\Facades\BeverlySearch;

class SearchCarton extends Component
{
    use WireUiActions;
    public CheckBoxForm $form;
    public $tenant;
    public bool $showExtraForm = false;
    public Collection $pos;
    public Collection $carton_numbers;
    public ?array $boxData = [
        'box_code' => null,
        'po' => null,
        'carton_number' => null,
        'selectedPo' => null,
        'selectedCartonNumber' => null
    ];

    public function render()
    {
        return view('beverly::livewire.carton-box.search-carton');
    }
    public function resetBoxData()
    {
        $this->boxData = [
            'box_code' => null,
            'po' => null,
            'carton_number' => null,
            'selectedPo' => null,
            'selectedCartonNumber' => null
        ];
    }
    public function successNotification(): void
    {
        $this->notification()->send([
            'icon' => 'success',
            'title' => 'Success Notification!',
            'description' => 'This is a description.',
        ]);
    }

    public function check(){
        $value = $this->form->all();
        $box_code =  $value['box_code'];
        $box = BeverlySearch::searchCarton($box_code,  $value['po'],  $value['carton_number']);


        if (empty( $value['box_code'])) {
            return $this->dispatch('swal', [
                'title' => 'Missing carton box barcode',
                'text' => 'Please scan the carton box barcode',
                'icon' => 'warning',

            ]);
        }
        if (empty($box) || $box === 'not found') {
            $this->resetBoxData();

            return $this->dispatch('swal', [
                'title' => 'Carton box not found!',
                'text' => 'Please check to your admin for available this carton.',
                'icon' => 'error',

            ]);
            //return $this->alert = true;
        }
        if ($box === 'multiple') {

            $this->dispatch('swal', [
                'toast' => true,
                'position'=> 'top-end',
                'showConfirmButton' => false,
                'timer'=> 6000,
                'timerProgressBar' => true,
                'title' => 'Multiple carton boxes found!',
                'text' => 'Please select PO or/and Carton Number for additional filter parameter.',
                'icon' => 'warning',

            ]);
            return $this->showExtraForm = true;
        }
        if (!empty($box->is_completed)) {
            if ($box->is_completed === true || $box->is_completed === 'true') {

                return redirect(route('filament.beverly.completed.carton.release', ['carton' => $box->id]));
            }
        }
        return redirect(route('filament.beverly.validation.polybag.release', ['carton' => $box->id]));
    }


}
