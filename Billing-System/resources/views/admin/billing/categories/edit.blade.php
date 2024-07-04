{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}

{{-- Billing System made by Kevko - https://mrkevko.nl --}}
@extends('layouts.admin')

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Manage your billing settings</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">Index</li>
    </ol>
@endsection

@section('content')
<div class="row">
    @include('admin.billing.nav')
  @foreach ($category as $setting)
    <form method="POST" action="{{ route('admin.billing.categories.update', $setting->id) }}" enctype="multipart/form-data">
      <div class="col-xs-12">
        <div class="box box-secondary">
          <div class="box-header with-border">
            <h3 class="box-title">General Information</h3>
          </div>
          <div class="box-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group col-md-4">
                    <label class="control-label">Category Name</label>
                    <div>
                      <input type="text" class="form-control" name="name" placeholder="Category Name" value="{{ $setting->name }}">
                    </div>
                  </div>
                  <div class="form-group col-md-4">
                    <label class="control-label">Priority</label>
                    <div>
                      <input type="number" class="form-control" name="priority" placeholder="5" value="{{ $setting->priority }}">
                    </div>
                  </div>
                  <div class="form-group col-md-4">
                    <label class="control-label">Visible</label>
                    <select name="visible" class="form-control">
                      <option value="0" @if ($setting->visible == 0) selected @endif>No</option>
                      <option value="1" @if ($setting->visible == 1) selected @endif>Yes</option>
                    </select>
                  </div>
                  <div class="form-group col-md-12">
                    <label class="control-label">Description</label>
                    <div>
                      <textarea class="form-control" name="description" id="description" rows="4">{!! $setting->description !!}</textarea>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 text-center">
                  <div class="form-group col-md-12">
                    <div class="image-upload">
                      <label for="imgInp">
                        <img id="img-upload" src="{{ $setting->img }}" height="200" width="200" style="border-radius: 50%;">
                        <input type="file" name="select_file" id="imgInp" />
                      </label>
                    </div>
                    <p class="text-muted">Select File for Upload <strong>jpg, jpeg, png, gif</strong></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer with-border">
              @csrf
              <button type="submit" class="btn btn-sm btn-success pull-right">Update</button>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </form>
</div>

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script>
$(document).ready( function() {
        $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
        });

        $('.btn-file :file').on('fileselect', function(event, label) {
            
            var input = $(this).parents('.input-group').find(':text'),
                log = label;
            
            if( input.length ) {
                input.val(log);
            } else {
                if( log ) alert(log);
            }
        
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    $('#img-upload').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function(){
            readURL(this);
        });     
});
</script>
@endsection

@section('footer-scripts')
    @parent
    <script src="//cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>

    <script>
        function MinHeightPlugin(editor) {
            this.editor = editor;
        }

        MinHeightPlugin.prototype.init = function() {
            this.editor.ui.view.editable.extendTemplate({
                attributes: {
                    style: {
                        minHeight: '300px'
                    }
                }
            });
        };

        ClassicEditor.builtinPlugins.push(MinHeightPlugin);
        ClassicEditor
            .create( document.querySelector( '#description' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
    {!! Theme::js('vendor/lodash/lodash.js') !!}
    {!! Theme::js('js/admin/new-server.js') !!}
@endsection
