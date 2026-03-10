<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaseCalculatorController extends Controller
{
    use ResolvesFrontendView;

    public function show(Request $request): View
    {
        return view($this->frontendView($request, 'tools.lease-calculator'));
    }
}
