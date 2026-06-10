@extends('layouts.back-end.app')

@section('title', translate('category'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0">
                <img src="{{asset('assets/back-end/img/brand-setup.png')}}" class="mb-1 mr-1" alt="">
                @if($category['position'] == 1)
                    {{translate('sub')}}
                @elseif($category['position'] == 2)
                    {{translate('sub_Sub')}}
                @endif
                {{translate('category')}}
                {{translate('update')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- <div class="card-header">
                        {{ translate('category_form')}}
                    </div> -->
                    <div class="card-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.category.update',[$category['id']])}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @php($language=\App\Model\BusinessSetting::where('type','pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item text-capitalize">
                                        <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}"
                                           href="#"
                                           id="{{$lang}}-link">{{\App\CPU\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class="{{ $category['parent_id']==0 || $category['position'] == 1 ? 'col-lg-6':'col-12' }}">
                                    @foreach(json_decode($language) as $lang)
                                    <div>
                                        <?php
                                        if (count($category['translations'])) {
                                            $translate = [];
                                            foreach ($category['translations'] as $t) {
                                                if ($t->locale == $lang && $t->key == "name") {
                                                    $translate[$lang]['name'] = $t->value;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                            id="{{$lang}}-form">
                                            <label class="title-color">{{translate('category_Name')}}
                                                ({{strtoupper($lang)}})</label>
                                            <input type="text" name="name[]"
                                                value="{{$lang==$default_lang?$category['name']:($translate[$lang]['name']??'')}}"
                                                class="form-control category-name"
                                                placeholder="{{translate('new_Category')}}" {{$lang == $default_lang? 'required':''}}>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    </div>
                                    @endforeach

                                    <div class="form-group">
                                        <label class="title-color">{{translate('slug')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="slug" class="form-control" id="category-slug"
                                            value="{{$category->slug}}"
                                            placeholder="{{translate('slug')}}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="title-color" for="priority">{{translate('priority')}}</label>
                                        <select class="form-control" name="priority" id="" required>
                                            @for ($i = 0; $i <= 10; $i++)
                                            <option
                                            value="{{$i}}" {{$category['priority']==$i?'selected':''}}>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    
                                <!--image upload only for main category-->
                                @if($category['parent_id']==0 || ($category['position'] == 1 && theme_root_path() == 'theme_aster'))
                                    <div class="from_part_2">
                                        <label class="title-color">{{translate('category_Logo')}}</label>
                                        <span class="text-info">({{translate('ratio')}} 1:1)</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileEg1"
                                                   class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="customFileEg1">{{translate('choose_File')}}</label>
                                        </div>
                                    </div>
                                    <div class="from_part_2 mt-4">
                                        <label class="title-color">{{translate('category_Popup_Image')}} ({{translate('optional')}})</label>
                                        <div class="custom-file text-left">
                                            <input type="file" name="popup_image" id="customFileEg2"
                                                   class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="customFileEg2">{{translate('choose_File')}}</label>
                                        </div>
                                    </div>
                                    <div class="from_part_2 mt-2">
                                        <label class="title-color">{{translate('popup_Image_Link')}} ({{translate('optional')}})</label>
                                        <input type="url" name="popup_image_link" class="form-control"
                                               value="{{$category['popup_image_link']}}"
                                               placeholder="https://example.com">
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-5 mt-lg-0 from_part_2">
                                    <div class="form-group">
                                        <center>
                                            <img class="upload-img-view mb-4"
                                                    id="viewer"
                                                    onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                                    src="{{asset('storage/category')}}/{{$category['icon']}}"
                                                    alt=""/>
                                            <div class="position-relative d-inline-block">
                                                <img class="upload-img-view"
                                                    id="viewer2"
                                                    onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                                    src="{{asset('storage/category')}}/{{$category['popup_image']}}"
                                                    alt="popup image"/>
                                                @if($category['popup_image'])
                                                    <a class="btn btn-outline-danger btn-sm square-btn position-absolute"
                                                       style="top:-5px;right:-5px;"
                                                       href="{{route('admin.category.remove-popup-image', [$category['id']])}}">
                                                        <i class="tio-delete"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </center>
                                    </div>
                                </div>
                                @endif
                                @if($category['position'] == 2 || ($category['position'] == 1 && theme_root_path() != 'theme_aster'))
                                        <div class="d-flex justify-content-end gap-3">
                                            <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset')}}</button>
                                            <button type="submit" class="btn btn--primary px-4">{{ translate('update')}}</button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($category['parent_id']==0 || ($category['position'] == 1 && theme_root_path() == 'theme_aster'))
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="reset" id="reset" class="btn btn-secondary px-4">{{ translate('reset')}}</button>
                                    <button type="submit" class="btn btn--primary px-4">{{ translate('update')}}</button>
                                </div>
                            @endif
                            <div class="card mt-3">
                                        <div class="card-header">
                                            <h5 class="mb-0">{{translate('blog_Section')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label class="title-color">{{translate('blog_Title')}}</label>
                                                <input type="text" name="blog_title" class="form-control" id="blog-title"
                                                    value="{{$category->blog_title}}"
                                                    placeholder="{{translate('blog_Title')}}">
                                            </div>
                                            <div class="form-group">
                                                <label class="title-color">{{translate('blog_Slug')}}</label>
                                                <input type="text" name="blog_slug" class="form-control" id="blog-slug"
                                                    value="{{$category->blog_slug}}"
                                                    placeholder="{{translate('blog_Slug')}}">
                                            </div>
                                            <div class="form-group">
                                                <label class="title-color">{{translate('blog_Description')}}</label>
                                                <textarea name="blog_description" class="textarea editor-textarea">{{$category->blog_description}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>
        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        function readURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer2').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg2").change(function () {
            readURL2(this);
        });

        $('.category-name').on('keyup keypress change', function() {
            var name = $(this).val();
            var slug = name.toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^a-z0-9-]/g, '')
                .replace(/-+/g, '-');
            $('#category-slug').val(slug);
        });

        $('#blog-title').on('keyup keypress change', function() {
            var name = $(this).val();
            var slug = name.toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^a-z0-9-]/g, '')
                .replace(/-+/g, '-');
            $('#blog-slug').val(slug);
        });
    </script>

    {{--ck editor--}}
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        if (window.CKEDITOR) {
            CKEDITOR.env.isCompatible = true;
            $('.textarea').ckeditor({
                contentsLangDirection: '{{Session::get('direction')}}',
            });
        }
    </script>
    {{--ck editor--}}
@endpush




