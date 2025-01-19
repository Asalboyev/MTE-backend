@extends('layouts.app')

@section('links')

<script>
    window.onload = function() {
        var add_post = new Dropzone("div#dropzone", {
            url: "{{ url('/admin/upload_from_dropzone') }}",
            paramName: "file",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            addRemoveLinks: true,
            maxFiles: 10,
            maxFilesize: 2, // MB
            success: (file, response) => {
                let input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('value', response.file_name);
                input.setAttribute('name', 'dropzone_images[]');

                let form = document.getElementById('add');
                form.append(input);
            },
            removedfile: function(file) {
                file.previewElement.remove();
                if (file.xhr) {
                    let data = JSON.parse(file.xhr.response);
                    let removing_img = document.querySelector('[value="' + data.file_name + '"]');
                    removing_img.remove();
                } else {
                    let data = file.name.split('/')[file.name.split('/').length - 1]
                    let removing_img = document.querySelector('[value="' + data + '"]');
                    removing_img.remove();
                }
            },
            error: function(file, message) {
                alert(message);
                this.removeFile(file);
            },

            // change default texts
            dictDefaultMessage: "Перетащите сюда файлы для загрузки",
            dictRemoveFile: "Удалить файл",
            dictCancelUpload: "Отменить загрузку",
            dictMaxFilesExceeded: "Не можете загружать больше",

            @if(old('dropzone_images'))
            init: function() {
                var thisDropzone = this;

                // document.querySelector('.dropzone').classList.add('dz-max-files-reached');

                @foreach(old('dropzone_images') as $img)

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('value', '{{ $img }}');
                input.setAttribute('name', 'dropzone_images[]');

                var form = document.getElementById('add');
                form.append(input);

                var mockFile = {
                    name: '{{ $img }}',
                    size: 1024 * 512,
                    accepted: true
                };

                thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                thisDropzone.options.thumbnail.call(thisDropzone, mockFile, '/upload/images/{{ $img }}');
                thisDropzone.files.push(mockFile)

                @endforeach
            }
            @endif
        });
    };
</script>

@endsection

@section('content')
<!-- HEADER -->
<div class="header">
    <div class="container-fluid">

        <!-- Body -->
        <div class="header-body">
            <div class="row align-items-end">
                <div class="col">

                    <!-- Title -->
                    <h1 class="header-title">
                        {{ $title }}
                    </h1>

                </div>
            </div> <!-- / .row -->
        </div> <!-- / .header-body -->
        @include('app.components.breadcrumb', [
        'datas' => [
        [
        'active' => false,
        'url' => route($route_name.'.index'),
        'name' => $title,
        'disabled' => false
        ],
        [
        'active' => true,
        'url' => '',
        'name' => 'Добавление',
        'disabled' => true
        ],
        ]
        ])
    </div>
</div> <!-- / .header -->

<!-- CARDS -->
<div class="container-fluid">
    <form method="post" action="{{ route($route_name . '.store') }}" enctype="multipart/form-data" id="add">
        @csrf
        <div class="row">
            <div class="col-12">
                <!-- Button -->
                <div class="model-btns d-flex justify-content-end mb-3">
                    <a href="{{ route('posts_categories.index') }}" type="button" class="btn btn-secondary">Отмена</a>
                    <button type="submit" class="btn btn-primary ms-2">Сохранить</button>
                </div>
                <div class="card mw-50">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    @foreach($langs as $lang)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $lang->code }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $lang->code }}" type="button" role="tab" aria-controls="{{ $lang->code }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $lang->title }}</button>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    @foreach($langs as $lang)
                                    <div class="tab-pane mt-3 fade {{ $loop->first ? 'show active' : '' }}" id="{{ $lang->code }}" role="tabpanel" aria-labelledby="{{ $lang->code }}-tab">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-2">
                                                    <label for="title" class="form-label {{ $lang->code == $main_lang->code ? 'required' : '' }}">Заголовок</label>
                                                </div>
                                                <div class="col-10">
                                                    <input type="text" {{ $lang->code == $main_lang->code ? 'required' : '' }} class="form-control @error('title.'.$lang->code) is-invalid @enderror" name="title[{{ $lang->code }}]" value="{{ old('title.'.$lang->code) }}" id="title" placeholder="Заголовок...">
                                                    @error('title.'.$lang->code)
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-2">
                                                    <label for="desc" class="form-label">Описание</label>
                                                </div>
                                                <div class="col-10">
                                                    <textarea name="desc[{{ $lang->code }}]" id="desc" cols="30" rows="10" class="form-control @error('desc.'.$lang->code) is-invalid @enderror ckeditor" name="desc[{{ $lang->code }}]" placeholder="Описание...">{{ old('desc.'.$lang->code) }}</textarea>
                                                    @error('desc.'.$lang->code)
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-2">
                                                    <label for="info" class="form-label">Xapaктеристика</label>
                                                </div>
                                                <div class="col-10">
                                                    <textarea name="info[{{ $lang->code }}]" id="info" cols="30" rows="10" class="form-control @error('info.'.$lang->code) is-invalid @enderror ckeditor" name="info[{{ $lang->code }}]" placeholder="Описание...">{{ old('info.'.$lang->code) }}</textarea>
                                                    @error('info.'.$lang->code)
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0">SEO данные</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="meta_desc" class="form-label">Meta Описание</label>
                                        </div>
                                        <div class="col-10">
                                            <textarea id="meta_desc" cols="4" rows="4" class="form-control @error('meta_desc.'.$lang->code) is-invalid @enderror" name="meta_desc[{{ $lang->code }}]">{{ old('meta_desc.'.$lang->code) ?? $product->meta_desc[$lang->code] ?? null }}</textarea>
                                            @error('meta_desc.'.$lang->code)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="meta_keywords" class="form-label">Meta ключи</label>
                                        </div>
                                        <div class="col-10">
                                            <textarea id="meta_keywords" cols="4" rows="4" class="form-control @error('meta_keywords.'.$lang->code) is-invalid @enderror" name="meta_keywords[{{ $lang->code }}]">{{ old('meta_keywords.'.$lang->code) ?? $product->meta_keywords[$lang->code] ?? null }}</textarea>
                                            @error('meta_keywords.'.$lang->code)
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mw-50">
                    <div class="card-header">
                        <h2 class="mb-0">Дополнительные данные</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">



                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="categories" class="form-label">Категории</label>
                                        </div>
                                        <div class="col-10">
                                            <select class="form-control @error('categories') is-invalid @enderror" data-choices='{"removeItemButton": true}' multiple name="categories[]">
                                                @foreach ($all_categories as $key => $item)
                                                <option value="{{ $item->id }}" style="margin-left: 12px" {{ (old('categories') ? in_array($item->id, old('categories')) : '') ? 'selected' : '' }}>{{ $item->title[$main_lang->code] }}</option>
                                                @endforeach
                                            </select>
                                            @error('categories')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <!-- Dropzone -->
                                    <div class="row">
                                        <div class="col-2">
                                            <label for="dropzone" class="form-label">Пост</label>
                                        </div>
                                        <div class="col-10">
                                            <div class="dropzone dropzone-multiple" id="dropzone"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Button -->
            <div class="model-btns d-flex justify-content-end mb-5">
                <a href="{{ route('posts_categories.index') }}" type="button" class="btn btn-secondary">Отмена</a>
                <button type="submit" class="btn btn-primary ms-2">Сохранить</button>
            </div>
        </div>
    </form>
</div>
@endsection
