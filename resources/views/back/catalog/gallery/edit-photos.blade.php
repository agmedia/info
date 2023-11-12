@push('gallery_css')
    <style>
        .fileContainer {
            overflow: hidden;
            position: relative;
        }

        .fileContainer [type=file] {
            cursor: inherit;
            display: block;
            font-size: 999px;
            filter: alpha(opacity=0);
            min-height: 34px;
            min-width: 100%;
            opacity: 0;
            position: absolute;
            right: 0;
            text-align: right;
            top: 0;
        }

        .fileContainer {
            background: #E3E3E3;
            float: left;
            padding: .5em 1.5rem;
            height: 34px;
        }

        .fileContainer [type=file] {
            cursor: pointer;
        }

        img.preview {
            width: 200px;
            background-color: white;
            border: 1px solid #DDD;
            padding: 5px;
        }
    </style>
@endpush

<div>
    <div class="row">
        <div class="col-12">
            <div class="file-drop-area">
                <label for="files" style="display: block;padding: 1rem 2rem;border: 1px solid #CCCCCC;background-color: #eee;text-align: center;cursor: pointer;">{{ __('Odaberite Fotografije Galerije') }}</label>
                <input name="files[][image]" id="files" type="file" multiple>
            </div>
        </div>
    </div>

    <div class="row items-push" id="sortable">
        @if (isset($gallery))

            <div class="col-sm-12">
                <div class="row items-push" id="new-images">
                    @if (! empty($existing))
                        @foreach($existing as $key => $image)

                            <div class="col-sm-12 animated fadeIn mb-0 p-3 ribbon ribbon-left ribbon-bookmark ribbon-crystal" id="{{ 'image_id_' . $image['id'] }}">
                                <div class="row form-group mt-2">
                                    <div class="col-md-4">
                                        <div class="options-container fx-item-zoom-in fx-overlay-zoom-out">
                                            <div class="slim"
                                                 data-ratio="free"
                                                 data-max-file-size="2"
                                                 data-meta-type="gallery"
                                                 data-meta-type_id="{{ $gallery->id }}"
                                                 data-meta-image_id="{{ $image['id'] }}"
                                                 data-will-remove="removeImage"
                                            >
                                                <img src="{{ asset($image['image']) }}" alt="{{ 'image_' . $image['id'] }}"/>
                                                <input type="file" name="slim[{{ $image['id'] }}][image]"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 pl-4">
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <div class="form-group mb-2">
                                                    <label for="title-input" class="w-100">Naziv fotografije</label>
                                                    <input type="text" class="form-control" id="title-input" name="slim[{{ $image['id'] }}][title]" value="{{ $existing[$key]['title'] }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="alt-input" class="w-100">Alternativni tekst fotografije</label>
                                                <input type="text" class="form-control" id="alt-input" name="slim[{{ $image['id'] }}][alt]" value="{{ $existing[$key]['alt'] }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-9 text-right font-size-sm pt-2" >Redosljed</label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control js-tooltip-enabled" name="slim[{{ $image['id'] }}][sort_order]" value="{{ $image['sort_order'] }}" data-toggle="tooltip" data-placement="top" title="Sort Order">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <div class="custom-control custom-checkbox custom-checkbox-square custom-control-success mb-1">
                                                    <input type="checkbox" class="custom-control-input" id="check-published[{{ $image['id'] }}]" name="slim[{{ $image['id'] }}][published]" @if($image['published']) checked @endif>
                                                    <label class="custom-control-label" for="check-published[{{ $image['id'] }}]">Vidljivost foto.</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                        <input type="hidden" name="images_order" id="images-order">
                    @endif
                </div>
            </div>
        @else
            <div class="row items-push" id="new-images"></div>
        @endif
    </div>

</div>

@push('gallery_scripts')

    <script>
        /*let el = $('#max');

        el.maxlength({
            alwaysShow: true,
            threshold: el.data('threshold') || 10,
            warningClass: el.data('warning-class') || 'badge badge-warning',
            limitReachedClass: el.data('limit-reached-class') || 'badge badge-danger',
            placement: el.data('placement') || 'bottom',
            preText: el.data('pre-text') || '',
            separator: el.data('separator') || '/',
            postText: el.data('post-text') || ''
        });*/
    </script>

    <script>
        //
        let blocks = "{{ (isset($gallery) && isset($existing)) ? count($existing) : 0 }}";
        let created_id = 0;
        // get a reference to the file drop area and the file input
        var fileDropArea = document.querySelector('.file-drop-area');
        var fileInput = fileDropArea.querySelector('input');
        var fileInputName = fileInput.name;

        // listen to events for dragging and dropping
        fileDropArea.addEventListener('dragover', handleDragOver);
        fileDropArea.addEventListener('drop', handleDrop);
        fileInput.addEventListener('change', handleFileSelect);

        function handleDragOver(e) {
            e.preventDefault();
        }
        function handleDrop(e) {
            e.preventDefault();
            handleFileItems(e.dataTransfer.items || e.dataTransfer.files);
        }
        function handleFileSelect(e) {
            handleFileItems(e.target.files);
        }

        // loops over a list of items
        function handleFileItems(items) {
            let l = items.length;
            for (let i=0; i<l; i++) {
                handleItem(items[i]);
            }
        }

        function handleItem(item) {
            // get file from item
            let file = item;
            if (item.getAsFile && item.kind == 'file') {
                file = item.getAsFile();
            }

            handleFile(file);
        }

        // now we're sure each item is a file
        function handleFile(file) {
            createCropper(file);
        }

        // create an Image Cropper for each passed file
        function createCropper(file) {
            // create container element for cropper
            let holder = document.getElementById('new-images');

            let col = document.createElement('div');
            col.className = 'col-lg-3 col-md-4 animated fadeIn mb-5 p-3 ribbon ribbon-left ribbon-bookmark ribbon-crystal';

            let cropper = document.createElement('div');

            // insert this element after the file drop area
            col.insertAdjacentElement('afterbegin', cropper);
            col.insertAdjacentHTML('beforeend', '<div class="row form-group mt-2">\n' +
                '                                    <div class="col-sm-4" style="padding-right: 0;">\n' +
                '                                        <input type="text" class="form-control js-tooltip-enabled" name="files[' + created_id + '][sort_order]" value="' + blocks + '" data-toggle="tooltip" data-placement="top" title="Sort Order">\n' +
                '                                    </div>\n' +
                '                                    <div class="col-sm-8 text-right">\n' +
                '                                        <label class="css-control css-control-primary css-radio mt-2">\n' +
                '                                            <input type="radio" class="css-control-input" name="files[' + created_id + '][default]" value="image/' + file.name + '">\n' +
                '                                            <span class="mr-2">Default</span> <span class="css-control-indicator"></span>\n' +
                '                                        </label>\n' +
                '                                    </div>\n' +
                '                                </div>');

            holder.insertAdjacentElement('beforeend', col);

            // create a Slim Cropper
            Slim.create(cropper, {
                ratio: 'free',
                //size: '600,800',
                maxFileSize: '2',
                service: false,
                meta: {
                    type: 'gallery',
                    type_id: "{{ isset($gallery) ? $gallery->id : '' }}",
                    image_id: 0
                },
                defaultInputName: fileInputName,
                didInit: function() {
                    // load the file to our slim cropper
                    this.load(file);

                },
                didRemove: function(data, slim) {
                    col.parentNode.removeChild(col)
                    // destroy the slim cropper
                    this.destroy();

                }
            });

            blocks++;
            created_id++;
        }

        function handleXHRRequest(xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', "{{ csrf_token() }}");

            console.log(fileInput)
        }

        function removeImage(data, slim) {
            if (data.meta.hasOwnProperty('image_id')) {
                axios.post("{{ route('gallery.destroy.image') }}", { data: data.meta.image_id })
                    .then((response) => {
                        successToast.fire({
                            text: 'Fotografija je uspješno izbrisana',
                        })

                        let elem = document.getElementById('image_id_' + data.meta.image_id);

                        elem.parentNode.removeChild(elem);
                    })
                    .catch((error) => {
                        errorToast.fire({
                            text: 'Greška u brisanju fotografije..! Molimo pokušajte ponovo.',
                        })
                    })
            } else {
                errorToast.fire({
                    text: 'Glavna slika se ne može izbrisati..!',
                })
            }

            //slim.destroy();
        }

        // hide file input, we can now upload with JavaScript
        fileInput.style.display = 'none';

        // remove file input name so it's value is
        // not posted to the server
        fileInput.removeAttribute('name');
    </script>

@endpush
