<?php

namespace Xbigdaddyx\Beverly\Controller;


use  Xbigdaddyx\Accuracy\Models\CartonBox;
use  Xbigdaddyx\Accuracy\Models\Polybag;
use Domain\Users\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Xbigdaddyx\Beverly\Models\CartonBox as ModelsCartonBox;
use Xbigdaddyx\Fuse\Domain\User\Models\User as ModelsUser;
use Xbigdaddyx\Beverly\Controller\Controller as ControllerSupport;
class VerificationController extends ControllerSupport
{
    public function index(Request $request, $carton)
    {


        return view('beverly::pages.validation', ['carton' => $carton]);
    }
    public function completed($carton)
    {

        $carton_detail = ModelsCartonBox::with('completedBy')->find($carton);
        $user = ModelsUser::find($carton_detail->completed_by);
        return view('beverly::pages.completed', ['carton' => $carton_detail, 'user' => $user]);
    }
}
