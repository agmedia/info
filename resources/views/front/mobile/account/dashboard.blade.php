@extends('front.mobile.layouts.store')

@section('title', __('ui.account.dashboard.page_title'))
@section('header_title', __('ui.account.breadcrumb.account'))
@section('page_title', __('ui.account.nav.dashboard'))

@section('content')
    <div class="card card-style bg-11" data-card-height="170">
        <div class="card-bottom ps-3 pb-3 pe-3">
            <p class="color-white opacity-70 mb-1">{{ __('ui.account.dashboard.cards.user') }}</p>
            <h2 class="color-white font-800 mb-0">{{ $user->name }}</h2>
            <p class="color-white opacity-70 mb-0">{{ $user->email }}</p>
        </div>
        <div class="card-overlay bg-black opacity-70"></div>
    </div>

    <div class="content mt-0 mb-1">
        <div class="row mb-0">
            <div class="col-6 pe-1">
                <a href="{{ route('account.profile') }}" class="card card-style mx-0 mb-2 p-3 d-block">
                    <h6 class="font-14 mb-1">{{ __('ui.account.nav.edit_account') }}</h6>
                    <h3 class="mb-0"><i class="fa fa-user font-18"></i></h3>
                </a>
            </div>
            <div class="col-6 ps-1">
                <div class="card card-style mx-0 mb-2 p-3 d-block">
                    <h6 class="font-14 mb-1">{{ __('Profile') }}</h6>
                    <h3 class="mb-0">{{ $profileCompletion }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <h4 class="mb-2">{{ __('Account overview') }}</h4>
            <p class="opacity-70 mb-0">{{ __('This info site account keeps profile data and communication preferences only. Shopping/order modules are disabled in this build.') }}</p>
        </div>
    </div>
@endsection
