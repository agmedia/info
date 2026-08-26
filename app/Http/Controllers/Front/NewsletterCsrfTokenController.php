<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterCsrfTokenController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()
            ->json(['token' => $request->session()->token()])
            ->header('Cache-Control', 'no-store, private');
    }
}
