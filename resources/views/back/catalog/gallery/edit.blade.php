@extends('back.layouts.backend')

@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/dropzone/min/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plugins/slim/slim.css') }}">

    @stack('gallery_css')
@endpush

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">{{ __('Edit') }}</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('galleries') }}">{{ __('Naslov') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Nova') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content content-full ">

        @include('back.layouts.partials.session')

        <form action="{{ isset($gallery) ? route('gallery.update', ['gallery' => $gallery]) : route('gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($gallery))
                {{ method_field('PATCH') }}
            @endif
            <div class="row">

                <div class="col-md-12">
                    <div class="block">
                        <div class="block-header block-header-default">
                            <a class="btn btn-light" href="{{ route('galleries') }}">
                                <i class="fa fa-arrow-left mr-1"></i> {{ __('Nazad') }}
                            </a>
                            <div class="block-options">
                                <div class="custom-control custom-switch custom-control-info block-options-item ml-4">
                                    <input type="checkbox" class="custom-control-input" id="featured-switch" name="featured" @if (isset($gallery) and $gallery->featured) checked @endif>
                                    <label class="custom-control-label" style="padding-top: 2px;" for="featured-switch">{{ __('Featured') }}</label>
                                </div>
                                <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                                    <input type="checkbox" class="custom-control-input" id="status-switch" name="status" @if (isset($gallery) and $gallery->status) checked @endif>
                                    <label class="custom-control-label" style="padding-top: 2px;" for="status-switch">{{ __('Status') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row justify-content-center push mb-4">
                                <div class="col-md-12">
                                    <div class="form-group mb-2">
                                        <label for="title-input" class="w-100">{{ __('Naslov Galerije') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title-input" name="title" placeholder="Upiši naslov" value="{{ isset($gallery) ? $gallery->title : old('title') }}">
                                        @error('title')
                                        <span class="text-danger font-italic">{{ __('Naslov je obvezan...') }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center push">
                                <div class="col-md-4">
                                    <div class="form-group mb-2">
                                        <label for="group-select">{{ __('Grupa') }}</label>
                                        <select class="js-select2 form-control" id="group-select" name="group" style="width: 100%;">
                                            <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                            @foreach ($groups as $group)
                                                <option value="{{ $group }}" {{ (isset($gallery->group) and $group == $gallery->group) ? 'selected="selected"' : '' }}>{{ strtoupper($group) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group mb-2">
                                        <label for="target-id-select">{{ __('Odaberi Vrijednost Nositelj') }}</label>
                                        <select class="js-select2 form-control" id="target-id-select" name="target_id" style="width: 100%;">
                                            <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                            @foreach ($targets as $target)
                                                <option value="{{ $target->id }}" {{ (isset($gallery) and $gallery->target_id == $target->id) ? 'selected="selected"' : '' }}>{{ $target->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center push mt-6">
                                <div class="col-md-12">
                                    @include('back.catalog.gallery.edit-photos')
                                </div>
                            </div>
                        </div>
                        <div class="block-content bg-body-light">
                            <div class="row">
                                <div class="col-md-6">
                                    @if (isset($gallery))
                                        <a href="{{ route('gallery.destroy', ['gallery' => $gallery]) }}" type="submit" class="btn btn-hero-danger my-2 js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="{{ __('Obriši') }}"
                                           onclick="event.preventDefault(); document.getElementById('delete-gallery-form{{ $gallery->id }}').submit();">
                                            <i class="fa fa-trash-alt"></i> {{ __('Obriši') }}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-hero-success mb-3 float-right">
                                        <i class="fas fa-save mr-1"></i> {{ __('Snimi') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        @if (isset($gallery))
            <form id="delete-gallery-form{{ $gallery->id }}" gallery="{{ route('gallery.destroy', ['gallery' => $gallery]) }}" method="POST" style="display: none;">
                @csrf
                {{ method_field('DELETE') }}
            </form>
        @endif
    </div>
@endsection

@push('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/plugins/slim/slim.kickstart.js') }}"></script>

    <script>
        $(() => {
            /**
             *
             */
            $('#group-select').select2({
                placeholder: '--  {{ __('Molimo odaberite') }} --',
                tags:        true
            });
            $('#target-id-select').select2({
                placeholder: '--  {{ __('Molimo odaberite') }} --'
            });
            /**
             *
             */
            $('#type-select').select2({
                placeholder:             '-- Molimo odaberite --',
                minimumResultsForSearch: Infinity
            });
            $('#type-select').on('change', function (e) {
                if (e.currentTarget.value == 'F') {
                    $('#discount-append-badge').text('kn');
                } else {
                    $('#discount-append-badge').text('%');
                }
            });

            {{--@if (isset($gallery))
            $('#group-select').attr("disabled", true);
            @endif--}}

        })
    </script>

    @stack('gallery_scripts')

@endpush
