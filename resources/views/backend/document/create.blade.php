@extends('layouts.single')

@section('style-head')
    @parent

    <link rel="stylesheet" href="{{ asset('vendor/redactor/redactor.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/document.css') }}">

    <style>
        #previewContainer{
            display: none;
        }
    </style>

    <style media="print">
        #previewContainer {
            display: block;
            margin:0;
            padding: 0;
        }

    </style>
@stop

@section('content')


<div class="container-fluid">
    <div class="col-md-10">
        <div class="editor-f4">
            {{ BootForm::open()->action(route('backend.document.store'))->attribute('class', 'hidden-print')->attribute('id', 'formEditor') }}
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <input type='hidden' name='case_id' value='{{$case->id}}'/>
                <input type='hidden' name='template_id' value='{{$template->id}}'/>
                <input type='hidden' name='title' value='{{$template->short_title}} {{$template->title}}'/>
                {{ BootForm::textarea('', 'content', ['id' => 'content'])->value($content) }}
            {{ BootForm::close() }}

            <div id="previewContainer" class="preview-container"></div>

        </div>
    </div>
    <div class="col-md-2 hidden-print" style="padding-top: 100px">
        <a class="btn btn-primary btn-block btn-save" href="#">Simpan</a>
        <hr />
        <a class="btn btn-default btn-block" href="#" id="btnPreview" data-toggle="modal" data-target=".modal-preview"><i class="fa fa-eye"></i> Preview</a>
        <a class="btn btn-success btn-block btn-print" href="#"><i class="fa fa-print"></i> Print</a>
                <hr />
        <a class="btn btn-default btn-block" href="{{ route('backend.cases.show', [$case['id']]) }}">Batal</a>
    </div>
</div>

<div class="modal fade modal-preview hidden-print" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg preview-f4 paper-f4">
    <div class="modal-content">
        <div class="modal-body preview-container">

        </div>
        <div class="modal-footer">
            <a class="btn btn-success btn-block btn-print" href="#"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
  </div>
</div>

@stop

@section('script-end')
    @parent
    <script src="{{ asset('vendor/redactor/redactor.min.js') }}"></script>
    <script src="{{ asset('vendor/redactor/plugins/table.js') }}"></script>
    <script src="{{ asset('vendor/redactor/plugins/fullscreen.js') }}"></script>
    <script src="{{ asset('vendor/redactor/plugins/underline.js') }}"></script>
    <script src="{{ asset('vendor/redactor/plugins/fontsize.js') }}"></script>
    <script>

        $(function()
        {
            $('#content').redactor({
                minHeight: 400,
                buttonSource: true,
                plugins: ['table', 'fullscreen', 'underline', 'fontsize'],
                imageUpload: '/skrip/uploadImage?_token={{ csrf_token() }}',
            });

            $('.btn-print').on('click', function (e) {
                e.preventDefault();
                $('.preview-container').html($('#content').redactor('code.get'))
                window.print();
            })

            $('.btn-save').on('click', function(e){
                e.preventDefault();
                $('#formEditor').submit();
            });

            $('#btnPreview').on('click', function (e) {
                e.preventDefault();
                $('.preview-container').html($('#content').redactor('code.get'))
            })

        });
    </script>
@stop
