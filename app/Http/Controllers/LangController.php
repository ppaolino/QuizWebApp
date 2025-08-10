<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class LangController extends Controller
{
    public function changeLanguage(Request $request, $language)
    {
        Session::put('language', $language);
        return redirect()->back();
    }
}
