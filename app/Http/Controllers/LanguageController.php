<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function setLocale(string $locale) {
        if (!in_array($locale, ['en', 'fr'])) {
            abort(400);
        }
     
        App::setLocale($locale);
     
        return back();
    }
}
