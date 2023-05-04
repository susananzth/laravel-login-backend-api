<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function show(): JsonResponse
    {
        $language = 'es';
        if (session('language')) {
            $language = session('language');
        } elseif (config('app.locale')) {
            $language = config('app.locale');
        }
        return response()->json([
            'language' => $language,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        session(['language' => $request->language]);
        app()->setLocale($request->language);
        return response()->json([
            'message' => 'Language changed successfully',
        ]);
    }
}
