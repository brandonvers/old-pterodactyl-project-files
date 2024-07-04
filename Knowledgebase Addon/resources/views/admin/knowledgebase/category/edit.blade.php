{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Edit Announcement
@endsection

@section('content-header')
    <h1>Knowledgebase<small>Here you can see information.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.knowledgebase') }}">Home</a></li>
        <li class="active">Knowledgebase</li>
    </ol>
@endsection

@section('content')
@if(Auth::user()->root_admin)
	<div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Categories Editor</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.knowledgebase') }}" class="btn btn-sm btn-primary">Go Back</a>
                    </div>
                </div>
                <form method="post" action="{{ route('admin.knowledgebase.category.update', $category->id) }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="title" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" />
                        </div>
                        <div class="form-group">
                            <label for="body" class="form-label"><A>Description</A></label>
                            <textarea name="description" id="description" rows="4" class="form-control">{{ $category->description }}</textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        <button class="btn btn-success pull-left" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@if(!Auth::user()->root_admin)
<div class="box-body" style="padding-left: 30px; background-color: rgba(255, 56, 56, 0.50) !important; border-radius: 20px; color: #d4d4d4 !important; padding-right: 30px; padding-bottom: 30px; margin-top: 5%;">
                    <h2 style="font-size: 22px; font-weight: 500;"><i class="fas fa-exclamation-triangle" style="color: rgb(255, 0, 0) !important;" aria-hidden="true"></i> Knowledgebase</h2>
                    <p>You can't use this page you aren't an admin user.</p>
                </div></div>
    @endif
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
            .create( document.querySelector( '#body' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
	
    <script>
        $('tr.server-description').on('mouseenter mouseleave', function (event) {
            $(this).prev('tr').css({
                'background-color': (event.type === 'mouseenter') ? '#f5f5f5' : '',
            });
        });
    </script>
    {!! Theme::js('js/frontend/serverlist.js') !!}	
@endsection
