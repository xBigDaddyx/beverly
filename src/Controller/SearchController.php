<?php

namespace Xbigdaddyx\Beverly\Controller;


use Xbigdaddyx\Beverly\Controller\Controller as ControllerSupport;
class SearchController extends ControllerSupport
{
    public function index()
    {
        return view('beverly::pages.search');
    }
}
