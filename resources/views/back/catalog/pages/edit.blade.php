@extends('back.layouts.backend')

@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/flatpickr/flatpickr.min.css') }}">

    <style>
        .cke_skin_kama .cke_button_CMDSuperButton .cke_label {
            display: inline;
        }
    </style>

@endpush

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Page edit</h1>
            </div>
        </div>
    </div>

    <div class="content content-full content-boxed">
        @include('back.layouts.partials.session')

        <form action="{{ isset($page) ? route('pages.update', ['page' => $page]) : route('pages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($page))
                {{ method_field('PATCH') }}
            @endif

            <div class="block">
                <div class="block-header block-header-default">
                    <a class="btn btn-light" href="{{ route('pages') }}">
                        <i class="fa fa-arrow-left mr-1"></i> BAck
                    </a>
                    <div class="block-options">
                        @if (auth()->user()->can('*'))
                            <div class="custom-control custom-switch custom-control-info block-options-item">
                                <input type="checkbox" class="custom-control-input" id="special-switch" name="special" {{ (isset($page) and $page->subgroup == 'special') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="special-switch">Special Page</label>
                            </div>
                        @endif
                        <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                            <input type="checkbox" class="custom-control-input" id="status-switch" name="status" {{ (isset($page) and $page->status) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="status-switch">Publish</label>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row justify-content-center push">
                        <div class="col-md-10">

                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title-input">Title</label>
                                        <input type="text" class="form-control" id="title-input" name="title" placeholder="Enter page title..." value="{{ isset($page) ? $page->title : old('title') }}" onkeyup="SetSEOPreview()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="group-select">Group</label>
                                        <select class="js-select2 form-control" id="group-select" name="group" style="width: 100%;">
                                            <option></option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group }}" {{ ((isset($page)) and ($page->subgroup == $group)) ? 'selected' : '' }}>{{ $group }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @if (isset($page) and $page->subgroup == 'special')
                                <div class="form-group row mb-2">
                                    <div class="col-md-3">
                                        <label for="resource-select">Model</label>
                                        <select class="js-select2 form-control" id="resource-select" name="resource" style="width: 100%;">
                                            <option></option>
                                            @foreach ($resources as $key => $resource)
                                                <option value="{{ $key }}" {{ ((isset($page)) and ($key == $page->resource)) ? 'selected' : '' }}>{{ $resource }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <label for="query-input">DB Query</label>
                                        <input type="text" class="form-control" id="query-input" name="query_string" placeholder="Custom upit u bazu ako je potrebno..." value="{{ isset($resource_data['query']) ? $resource_data['query'] : '' }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="description-editor">Description</label>
                                        <textarea style="visibility: hidden; height: 30px;" id="ace-input" name="description"></textarea>
                                        <pre id="editor-blade" style="height: 500px; width: 100%;">{{ isset($page) ? $page->description : '' }}</pre>
                                    </div>
                                </div>
                            @else
                                <textarea id="js-ckeditor" name="description">{!! isset($page) ? $page->description : old('description') !!}</textarea>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Meta Data - SEO</h3>
                </div>
                <div class="block-content">
                    <div class="row justify-content-center">
                        <div class="col-md-10 ">
                            <form action="be_pages_ecom_product_edit.html" method="POST" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="meta-title-input">Meta title</label>
                                    <input type="text" class="js-maxlength form-control" id="meta-title-input" name="meta_title" value="{{ isset($page) ? $page->meta_title : old('meta_title') }}" maxlength="70" data-always-show="true" data-placement="top">
                                    <small class="form-text text-muted">
                                        70 chars max
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="meta-description-input">Meta description</label>
                                    <textarea class="js-maxlength form-control" id="meta-description-input" name="meta_description" rows="4" maxlength="160" data-always-show="true" data-placement="top">{{ isset($page) ? $page->meta_description : old('meta_description') }}</textarea>
                                    <small class="form-text text-muted">
                                        160 chars max
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="slug-input">SEO link (url)</label>
                                    <input type="text" class="form-control" id="slug-input" name="slug" value="{{ isset($page) ? $page->slug : old('slug') }}" disabled>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="block-content bg-body-light">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-hero-success my-2">
                                <i class="fas fa-save mr-1"></i> Snimi
                            </button>
                        </div>
                        @if (isset($page) && $page->subgroup != '/')
                            <div class="col-md-6 text-right">
                                <a href="{{ route('pages.destroy', ['page' => $page]) }}" type="submit" class="btn btn-hero-danger my-2 js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Obriši" onclick="event.preventDefault(); document.getElementById('delete-page-form{{ $page->id }}').submit();">
                                    <i class="fa fa-trash-alt"></i> Obriši
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        @if (isset($page))
            <form id="delete-page-form{{ $page->id }}" action="{{ route('pages.destroy', ['page' => $page]) }}" method="POST" style="display: none;">
                @csrf
                {{ method_field('DELETE') }}
            </form>
        @endif
    </div>
@endsection

@push('js_after')
    <script src="{{ asset('js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- Page JS Helpers (CKEditor 5 plugins) -->
    <script>jQuery(function(){Dashmix.helpers(['flatpickr']);});</script>

    @if (isset($page) && $page->subgroup == 'special')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.30.0/ace.js"></script>

        <script>
            var editor = ace.edit("editor-blade");
            editor.setTheme("ace/theme/monokai");
            editor.session.setMode("ace/mode/php_laravel_blade");

            let input = document.getElementById('ace-input');
            editor.getSession().on("change", function () {
                input.value = editor.getSession().getValue();
            });
        </script>
    @endif

    <script>
        $(() => {
            $('#group-select').select2({
                placeholder: 'Odaberite ili upišite novu grupu...',
                tags: true,
                allowClear: true
            });

            $('#resource-select').select2({
                placeholder: '-- Molimo odaberite --',
                minimumResultsForSearch: Infinity
            });

            let rte = document.getElementById("js-ckeditor");

            if (rte) {
                rt_editor = CKEDITOR.replace('js-ckeditor');
            }
        })
    </script>

    <script>
        function SetSEOPreview() {
            let title = $('#title-input').val();
            $('#slug-input').val(slugify(title));
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#image-view')
                    .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

@endpush
