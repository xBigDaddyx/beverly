<?php

namespace Xbigdaddyx\Beverly\Livewire;

use App\Livewire\Forms\ValidateForm;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Xbigdaddyx\Beverly\Facades\BeverlyVerification;
use Xbigdaddyx\Beverly\Livewire\Forms\ValidationBoxForm;
use Xbigdaddyx\Beverly\Models\CartonBox;
use Xbigdaddyx\Beverly\Models\Polybag;
use Xbigdaddyx\Beverly\Models\Tag;
use Xbigdaddyx\BeverlyRatio\Facades\BeverlyRatio;
use Xbigdaddyx\BeverlySolid\Facades\BeverlySolid;
use Filament\Notifications\Notification;


class ValidationCarton extends Component
{

    use LivewireAlert;
    public $carton;
    public $type;
    public $polybags;
    public $polybag;
    public $tags;
    public bool $showTable = false;
    public bool $completed = false;
    public bool $polybagCompleted = false;
    public ValidationBoxForm $form;
    public $polybagForm = [
        'polybag_code' => null,
        'additional' => null
    ];
    public $tagForm = [
        'tag_code' => null,
    ];
    public function resetTagForm()
    {
        $this->tagForm['tag_code'] = null;
    }
    public function resetPolybagForm()
    {
        $this->polybagForm['polybag_code'] = null;
    }

    public function mount($carton)
    {
        session()->forget('carton');
        session()->forget('polybag');
        $this->carton = CartonBox::with('polybags', 'attributes')->find($carton);
        $this->polybags = $this->carton->polybags;
        session()->put('carton.id', $this->carton->id);

        if (session()->get('polybag.status') === null || empty(session()->get('polybag.status'))) {
            session()->put('polybag.status', 0);
        }

        if (session()->get('carton.max_quantity') === null || empty(session()->get('carton.max_quantity'))) {
            if (!Session::has('carton.first_polybag')) {

                session()->put('carton.first_polybag', $this->carton->polybags->sortBy('created_at')->first()->polybag_code ?? null);
            }
            if (!Session::has('carton.type')) {
                session()->put('carton.type', $this->carton->type);
            }

            session()->put('carton.validated', $this->polybags->count());
            session()->put('carton.max_quantity', $this->carton->quantity);
        }
        if ($this->carton->type === 'RATIO' || $this->carton->type === 'MIX') {
            $cartonBox = $this->carton;
            $this->tags =  Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
                $a->where('carton_box_id', $cartonBox->id);
            })->whereNull('taggable_id')->get();
            if (session()->get('carton.tags') === null || count(session()->get('carton.tags')) > 0 || empty(session()->get('carton.tags'))) {
                session()->put(
                    'carton.tags',
                    $this->tags
                );
            }
            if (session()->get('carton.attributes') === null || count(session()->get('carton.attributes')) > 0 || empty(session()->get('carton.attributes'))) {
                session()->put('carton.attributes', $this->carton->attributes->toArray());
                session()->put('carton.total_attributes', $this->carton->attributes->sum('quantity'));
            }
        }

        if ($this->carton->is_completed === true) {
            $this->completed = true;
            return redirect(route('filament.beverly.completed.carton.release', ['carton' => $this->carton->id]));
        }
    }
    public function updated($property,)
    {
        $value = $this->form->all();
        if ($property === 'form.polybag_barcode') {
            if (str_contains($value['polybag_barcode'], 'LPN')) {


                $explode = explode('&', $value->polybag_barcode ?? '');
                $sku = str_replace('item_number=', '', $explode[1]);
                $lpn = str_replace('lpn=', '', $explode[2]);

                $value['polybag_barcode'] = $sku;
                $value['additional'] = $lpn;


                //$this->polybagForm['box_code'] === $explode[]
            }
        }

        //dd($this->boxForm['box_code']);
    }

    public function toggleShowTable()
    {
        return $this->showTable = !$this->showTable;
    }
    public function render()
    {
        return view('beverly::livewire.carton-box.validation-carton');
    }
    #[On('validation')]
    public function changeCompleted($value)
    {

        if ($value === 'saved' || $value === 'validated') {

            $polybag_count = Polybag::where('carton_box_id', $this->carton->id)->count();
            $max_qty = (int)$this->carton->quantity;

            if ($polybag_count === $max_qty) {

                $this->completed = true;
                return redirect(route('filament.beverly.completed.carton.release', ['carton' => $this->carton->id]));
            }
        }
        return $this->completed = false;
    }

    public function validation()
    {
        $value = $this->form->all();
        switch ($this->carton->type) {
            case 'SOLID':

                $validate = BeverlySolid::verification($this->carton, $value['polybag_barcode'], 0, null, auth()->user(), $value['additional']);
                if (is_array($validate)) {
                    $this->dispatch('swal', $validate);
                }
                $this->form->reset();
                break;
            case 'RATIO':
                // jika parameter polybag kosong maka akan dibuatkan record polybagnya
                if ($this->polybag || empty($this->polybag)) {
                    $this->polybag = BeverlyRatio::createRatioPolybag($this->carton, $value['additional'], Auth::user(), $value['polybag_barcode']);
                }
                // merubah status polybag ke open
                $status_change = BeverlyRatio::updateStatusRatioPolybag($this->polybag, 'open');
                if ($status_change || $this->polybag->status === 'open') {
                    // melakukan verifikasi garment
                }
                break;
            case 'MIX':
                break;
            default:
        }
        // if ($this->carton->type === 'SOLID') {
        //     $validate = BeverlySolid::verification($this->carton, $value['polybag_barcode'], 0, null, auth()->user(), $value['additional']);
        // } elseif ($this->carton->type === 'RATIO' || $this->carton->type === 'MIX') {
        //     if (session()->get('polybag.status') === 1) {
        //         $validate = BeverlyRatio::verification($this->carton, $value['polybag_barcode'], 1, $value['tag_barcode'], auth()->user());
        //         if ($this->carton->type === 'MIX') {
        //             if ($validate === 'updated' && $this->carton->polybags->count() !== $this->carton->quantity) {
        //                 $this->form->reset();
        //                 session()->put('polybag.status', 0);
        //             }
        //             return redirect(route('filament.beverly.validation.polybag.release', ['carton' => $this->carton->id]));
        //         }
        //         $this->form->reset();
        //         return redirect(route('filament.beverly.validation.polybag.release', ['carton' => $this->carton->id]));
        //     }

        //     $validate = BeverlyRatio::verification($this->carton, $value['polybag_barcode'], 0, $value['tag_barcode'], auth()->user());
        // }



        if ($validate === 'validated') {
            BeverlyVerification::itemValidated($this->carton, $value['polybag_barcode'], auth()->user(), $value['additional']);

            $this->polybags = CartonBox::find($this->carton->id)->polybags;
            $this->dispatch('swal', [
                'toast' => true,
                'position' => 'top-end',
                'showConfirmButton' => false,
                'timer' => 6000,
                'timerProgressBar' => true,
                'title' => 'Polybag Validated!',
                'text' => 'Go for next!',
                'icon' => 'warning',

            ]);
            $this->form->reset();
        } elseif ($validate === 'invalid') {
            $this->dispatch('swal', [
                'title' => 'Invalid Polybag',
                'text' => 'Please check the polybag may wrong size or color.',
                'icon' => 'error',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);

            $this->resetPolybagForm();
        } elseif ($validate === 'polybag completed') {
            session()->put('polybag.status', 1);
            $this->dispatch('swal', [
                'title' => 'All garment validated',
                'text' => 'Next, Please scan the polybag/carton barcode to complete.',
                'icon' => 'success',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);
        } else if ($validate === 'incorrect') {
            $this->form->reset();
            $this->dispatch('swal', [
                'title' => 'Incorrect garment tag',
                'text' => 'Please check the garment may wrong size or color.',
                'icon' => 'error',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);
        } else if ($validate === 'max') {

            $this->form->reset();
            $this->dispatch('swal', [
                'title' => 'Maximum ratio reached',
                'text' => 'The ratio for this garment is reached maximum, please validate next ratio.',
                'icon' => 'warning',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);
        } else if ($validate === 'saved') {
            $cartonBox = $this->carton;
            $this->tags =  Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
                $a->where('carton_box_id', $cartonBox->id);
            })->with('attributable')->whereNull('taggable_id')->get();

            if ($this->carton->type === 'MIX') {

                if ($this->tags->count() === (int)$this->tags->first()->attributable->quantity) {
                    session()->put('polybag.status', 1);
                    $this->dispatch('swal', [
                        'title' => 'All garment validated',
                        'text' => 'Next, Please scan the polybag/carton barcode to complete.',
                        'icon' => 'success',
                        'allowOutsideClick' => false,
                        'showConfirmButton' => true,

                    ]);
                }
            }
            if ($this->carton->type === 'RATIO') {

                if ($this->tags->count() === (int)session()->get('carton.total_attributes')) {
                    session()->put('polybag.status', 1);
                    $this->dispatch('swal', [
                        'title' => 'All garment validated',
                        'text' => 'Next, Please scan the polybag/carton barcode to complete.',
                        'icon' => 'success',
                        'allowOutsideClick' => false,
                        'showConfirmButton' => true,

                    ]);
                }
            }

            $this->form->reset();
            $this->dispatch('swal', [
                'toast' => true,
                'position' => 'top-end',
                'showConfirmButton' => false,
                'timer' => 6000,
                'timerProgressBar' => true,
                'title' => 'Garment Validated!',
                'text' => 'Go for next!',
                'icon' => 'warning',

            ]);
        }
    }
}
