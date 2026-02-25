@extends('front.desktop.layouts.store')

@section('title', __('ui.account.dashboard.page_title'))

@section('content')
    @include('front.desktop.account.partials.breadcrumbs', ['items' => [
        ['label' => __('ui.account.breadcrumb.home'), 'url' => route('home')],
        ['label' => __('ui.account.breadcrumb.account'), 'url' => route('account.dashboard')],
        ['label' => __('ui.account.dashboard.title')],
    ]])

    <section class="mb-8 border border-slate-200 bg-slate-100 px-6 py-6 text-center">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">{{ __('ui.account.dashboard.title') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('ui.account.dashboard.subtitle') }}</p>
    </section>

    <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        @include('front.desktop.account.partials.nav', ['current' => 'dashboard'])

        <div class="space-y-8">
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <article class="border border-slate-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('ui.account.dashboard.cards.user') }}</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $user->email }}</p>
                    <a href="{{ route('account.profile') }}" class="mt-3 inline-flex border-b border-slate-900 text-sm font-semibold text-slate-900 hover:text-slate-700">{{ __('ui.account.nav.edit_account') }}</a>
                </article>

                <article class="border border-slate-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Profile Completion') }}</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $profileCompletion }}%</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ __('Keep your contact and billing data up to date for faster communication.') }}</p>
                </article>

                <article class="border border-slate-200 bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Account Actions') }}</p>
                    <div class="mt-2 space-y-2">
                        <a href="{{ route('account.profile') }}" class="block text-sm font-semibold text-slate-900 hover:text-slate-700">{{ __('Update profile') }}</a>
                        <a href="{{ route('contact.create') }}" class="block text-sm font-semibold text-slate-900 hover:text-slate-700">{{ __('Contact support') }}</a>
                    </div>
                </article>
            </div>

            <section class="border border-slate-200 bg-white p-6">
                <h2 class="text-2xl font-bold text-slate-900">{{ __('Account overview') }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ __('This info site account currently focuses on profile, privacy, and communication preferences. Orders and shopping features are intentionally disabled.') }}</p>
            </section>
        </div>
    </div>
@endsection
