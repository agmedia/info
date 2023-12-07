@extends('back.layouts.backend')

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Dashboard</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        @include('back.layouts.partials.session')
        <!-- Super-admin view -->
        @if (auth()->user()->can('*'))
            <div class="row">
                <div class="col-md-12">
                    <div class="block block-rounded block-mode-hidden">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Superadmin dashboard</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                            </div>
                        </div>
                        <div class="block-content">
                            <a href="{{ route('roles.set') }}" class="btn btn-hero-sm btn-rounded btn-hero-secondary mb-3 mr-3">Set Roles</a>
                            {{--<a href="{{ route('import.initial') }}" class="btn btn-hero-sm btn-rounded btn-hero-info mb-3 mr-3">Initial Import</a>--}}
                            <a href="{{ route('mailing.test') }}" class="btn btn-hero-sm btn-rounded btn-hero-info mb-3 mr-3">Mail Test</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Overview -->
        <div class="row row-deck">

            <div class="col-6 col-lg-3">
                <a class="block block-rounded block-link-shadow text-center" href="{{ route('blogs') }}">
                    <div class="block-content py-5">
                        <div class="font-size-h3 font-w600 text-success mb-1"></div>
                        <div class="item rounded-3 bg-body mx-auto my-3">
                            <i class="far fa-file-alt fa-lg text-primary"></i>
                        </div>
                        <p class="font-w600  text-muted  mb-0">
                            Blog
                        </p>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a class="block block-rounded block-link-shadow text-center" href="{{ route('pages') }}">
                    <div class="block-content py-5">
                        <div class="font-size-h3 font-w600 text-warning mb-1"></div>
                        <div class="item rounded-3 bg-body mx-auto my-3">
                            <i class="far fa-file-code fa-lg text-primary"></i>
                        </div>
                        <p class="font-w600  text-muted  mb-0">
                           Pages
                        </p>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a class="block block-rounded block-link-shadow text-center" href="{{ route('users') }}">
                    <div class="block-content py-5">
                        <div class="font-size-h3 font-w600 text-success mb-1"></div>
                        <div class="item rounded-3 bg-body mx-auto my-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                        <p class="font-w600  text-muted  mb-0">
                            Users
                        </p>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a class="block block-rounded block-link-shadow text-center" href="{{ route('widgets') }}">
                    <div class="block-content py-5">
                        <div class="font-size-h3 font-w600 text-success mb-1"></div>
                        <div class="item rounded-3 bg-body mx-auto my-3">
                            <i class="fas fa-hand-sparkles fa-lg text-primary"></i>
                        </div>
                        <p class="font-w600  text-muted  mb-0">
                            Widgets
                        </p>
                    </div>
                </a>
            </div>


        </div>

        <!-- Top Products and Latest Orders -->
     <!--   <div class="row">
            <div class="col-12">

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Recent Blogs</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                <i class="si si-refresh"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <table class="table table-borderless table-striped table-vcenter font-size-sm">
                            <tbody>
                            {{--@foreach ($orders as $order)
                                <tr>
                                    <td class="font-w600 text-center" style="width: 5%;">
                                        <a href="{{ route('orders.edit', ['order' => $order]) }}">{{ $order->id }}</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="{{ route('orders.edit', ['order' => $order]) }}">{{ $order->payment_fname . ' ' . $order->payment_lname }}</a>
                                    </td>
                                    <td class="text-right" style="width: 5%;">
                                        <span class="badge badge-pill badge-{{ $order->status->color }}">{{ $order->status->title }}</span>
                                    </td>
                                    <td class="font-w600 text-right" style="width: 20%;">{{ \App\Helpers\Currency::main($order->total, true) }}</td>
                                </tr>
                            @endforeach--}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->
    </div>
    <!-- END Page Content -->
@endsection

@push('js_after')

@endpush

