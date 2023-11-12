@extends('back.layouts.backend')

@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
@endpush

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">{{ __('Galerije') }}</h1>
                <a class="btn btn-hero-success my-2" href="{{ route('gallery.create') }}">
                    <i class="far fa-fw fa-plus-square"></i><span class="d-none d-sm-inline ml-1"> {{ __('Nova') }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="content">
        @include('back.layouts.partials.session')

        <!-- All Products -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">{{ __('Lista') }} ({{ $galleries->total() }})</h3>

            </div>


            <div class="block-content">
                <!-- All Products Table -->
                <div class="table-responsive">
                    <table class="table table-borderless table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 36px;">{{ __('br') }}</th>
                            <th class="text-left">{{ __('Naslov') }}</th>
                            <th>{{ __('Fotografije') }}</th>
                            <th>{{ __('Grupa') }}</th>
                            <th class="text-center" style="width: 10%;">{{ __('Featured') }}</th>
                            <th class="text-center" style="width: 10%;">{{ __('Status') }}</th>
                            <th class="text-right" style="width: 10%;">{{ __('Akcije') }}i</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($galleries as $gallery)
                            <tr>
                                <td class="font-size-sm">{{ $loop->iteration }}</td>
                                <td class="font-size-sm">
                                    <a class="font-w600" href="{{ route('gallery.edit', ['gallery' => $gallery]) }}">{{ $gallery->title }}</a>
                                </td>
                                <td class="font-size-sm">
                                    @foreach ($gallery->images()->limit(4)->get() as $image)
                                        <img class="img-avatar img-avatar32 ml-1" src="{{ asset($image->image) }}" alt="">
                                    @endforeach
                                </td>
                                <td class="font-size-sm">{{ $gallery->group }}</td>
                                <td class="text-center font-size-sm">
                                    @include('back.layouts.partials.status', ['status' => $gallery->featured, 'simple' => true])
                                </td>
                                <td class="text-center font-size-sm">
                                    @include('back.layouts.partials.status', ['status' => $gallery->status, 'simple' => true])
                                </td>
                                <td class="text-right font-size-sm">
                                    <a class="btn btn-sm btn-alt-secondary" href="{{ route('gallery.edit', ['gallery' => $gallery]) }}">
                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>
                                    <button class="btn btn-sm btn-alt-danger" onclick="event.preventDefault(); deleteItem({{ $gallery->id }}, '{{ route('gallery.destroy.api') }}');"><i class="fa fa-fw fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="font-size-sm text-center" colspan="7">
                                    <label for="">{{ __('Prazno') }}</label>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $galleries->links() }}
            </div>
        </div>
        <!-- END All Products -->
    </div>
    <!-- END Page Content -->

@endsection

@push('js_after')


    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Page JS Helpers (CKEditor 5 plugins) -->
    <script>jQuery(function(){Dashmix.helpers(['select2','datepicker']);});</script>
    <script>
        $(() => {
            $('#category-select').select2({
                placeholder: 'Odaberite kategoriju'
            });
            $('#author-select').select2({
                placeholder: 'Odaberite autora'
            });
            $('#publisher-select').select2({
                placeholder: 'Odaberite izdavača'
            });
        })
    </script>
    <script>
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>

@endpush
