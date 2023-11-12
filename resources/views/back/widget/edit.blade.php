@extends('back.layouts.backend')

@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endpush


@section('content')
    <div class="content" id="pages-app">

        @include('back.layouts.partials.session')

        <form action="{{ isset($widget) ? route('widget.update', ['widget' => $widget]) : route('widget.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <h2 class="content-heading"> <a href="{{ route('widgets') }}" class="mr-2 text-gray font-size-h4"><i class="si si-action-undo"></i></a>
                @if (isset($widget))
                    {{ method_field('PATCH') }}
                    Uredi Widget <small class="text-primary pl-4">{{ $widget->title }}</small>
                @else
                    Napravi Novi Widget
                @endif
                <button type="submit" class="btn btn-primary btn-sm float-right"><i class="fa fa-save mr-2"></i> Snimi</button>
            </h2>

            <div class="block block-rounded block-shadow">
                <div class="block-content">
                    <div class="row items-push">
                        <div class="col-lg-7">
                            <h5 class="text-black mb-0 mt-20">Generalne Informacije
                                <div class="form-group float-right mr-3">
                                    <div class="custom-control custom-switch custom-control-success">
                                        <input type="checkbox" class="custom-control-input" id="status-switch" name="status" @if (isset($widget) and $widget->status) checked @endif>
                                        <label class="custom-control-label" for="status-switch">Status widgeta</label>
                                    </div>
                                </div>
                            </h5>
                            <hr class="mb-30">

                            <div class="form-group row items-push mb-3">
                                <div class="col-md-8">
                                    <label for="title-input">Naziv widgeta @include('back.layouts.partials.required-star')</label>
                                    <input type="text" class="form-control" id="title-input" name="title" placeholder="Upišite naziv widgeta" value="{{ isset($widget) ? $widget->title : old('title') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="resource-select">Grupa stavki @include('back.layouts.partials.required-star')</label>
                                    <select class="form-control" id="resource-select" name="resource">
                                        <option></option>
                                        @foreach ($resources as $key => $resource)
                                            <option value="{{ $key }}" {{ (isset($widget) and $key == $widget->resource) ? 'selected="selected"' : '' }}>{{ $resource }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <h5 class="text-black mb-0 mt-20">Detalji Widgeta</h5>
                            <hr class="mb-30">

                            <div class="block">
                                <div class="block-content" style="background-color: #f8f9f9; border: 1px solid #e9e9e9; padding: 30px;">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group mb-3">
                                                <div class="custom-control custom-switch custom-control-success">
                                                    <input type="checkbox" class="custom-control-input" id="new-switch" name="new" @if (isset($resource_data['new']) and $resource_data['new']) checked @endif>
                                                    <label class="custom-control-label" for="new-switch">Uključi nove stavke <span class="font-size-sm text-muted">new</span></label>
                                                </div>
                                            </div>
                                            <div class="form-group mb-1">
                                                <div class="custom-control custom-switch custom-control-success">
                                                    <input type="checkbox" class="custom-control-input" id="popular-switch" name="popular" @if (isset($resource_data['popular']) and $resource_data['popular']) checked @endif>
                                                    <label class="custom-control-label" for="popular-switch">Uključi popularne stavke <span class="font-size-sm text-muted">popular</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <div class="form-group mb-1">
                                                <div class="custom-control custom-switch custom-control-info">
                                                    <input type="checkbox" class="custom-control-input" id="container-switch" name="container" @if (isset($resource_data['container']) and $resource_data['container']) checked @endif>
                                                    <label class="custom-control-label" for="container-switch">Uključi okvir s sjenom <span class="font-size-sm text-muted">container</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row items-push mt-3">
                                <div class="col-md-12">
                                    <label for="title-input">Oznaka</label>
                                    <input type="text" class="form-control" id="slug-input" name="slug" placeholder="Upišite oznaku widgeta" value="{{ isset($widget) ? $widget->slug : old('slug') }}">
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-5">
                            <h5 class="text-black mb-0 mt-20">Stavke Widgeta</h5>
                            <hr class="mb-30">

                            @if (isset($widget))
                                @livewire('back.marketing.action-group-list', ['group' => $widget->resource, 'list' => $resource_data['items_list'] ?: []])
                            @else
                                @livewire('back.marketing.action-group-list', ['group' => ''])
                            @endif
                        </div>

                        <div class="col-lg-12 mb-3">
                            <h5 class="text-black mb-0 mt-20">Editor</h5>
                            <hr class="mb-30">

                            <div class="form-group">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" value="add" name="query_data" {{ (isset($resource_data['query_data']) && $resource_data['query_data'] == 'add') ? 'checked' : '' }}>
                                    <label class="custom-control-label">Add to query</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" value="replace" name="query_data" {{ (isset($resource_data['query_data']) && $resource_data['query_data'] == 'replace') ? 'checked' : '' }}>
                                    <label class="custom-control-label">Replace query</label>
                                </div>
                            </div>

                            <input type="text" class="form-control" id="query-input" name="query_string" placeholder="Custom upit u bazu ako je potrebno..." value="{{ isset($resource_data['query']) ? $resource_data['query'] : '' }}">

                            <textarea style="visibility: hidden; height: 12px" id="ace-input" name="data"></textarea>
                            <pre id="editor-blade" style="height: 500px; width: 100%;">{{ (isset($data)) ? $data : '' }}</pre>
                        </div>
                    </div>
                </div>

                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save mr-2"></i> Snimi
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection


@push('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.30.0/ace.js" type="text/javascript" charset="utf-8"></script>

    <script>
        var editor = ace.edit("editor-blade");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/php_laravel_blade");

        let input = document.getElementById('ace-input');
        editor.getSession().on("change", function () {
            input.value = editor.getSession().getValue();
        });
    </script>

    <script>
        $(() => {
            $('#resource-select').select2({
                placeholder: '-- Molimo odaberite --',
                allowClear: true,
                minimumResultsForSearch: Infinity
            });
            $('#resource-select').on('change', function (e) {
                Livewire.emit('groupUpdated', e.currentTarget.value);
            });

            Livewire.on('list_full', () => {
                console.log('istina')
                $('#resource-select').attr("disabled", true);
            });
            Livewire.on('list_empty', () => {
                console.log('nije istina')
                $('#resource-select').attr("disabled", false);
            });

            @if (isset($widget) && ! empty($resource_data['items_list']))
                $('#resource-select').attr("disabled", true);
            @endif
        });
    </script>

@endpush
