<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * Retrieve the translation dictionaries for the requested language.
     * The language is automatically set by the SetLocale middleware
     * based on the 'Accept-Language' header or '?lang=' query param.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'locale' => app()->getLocale(),
            'translations' => [
                'home' => trans('home'),
                'products' => trans('products'),
            ]
        ]);
    }
}
