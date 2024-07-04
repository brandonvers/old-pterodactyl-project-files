{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Addons
@endsection

@section('content-header')
    <h1>Knowledgebase<small>Create questions.</small></h1>
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
                    <h3 class="box-title">Question Creator</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.knowledgebase.questions.index') }}" class="btn btn-sm btn-primary">Go Back</a>
                    </div>
                </div>
                <form method="post" action="{{ route('admin.knowledgebase.questions.create')  }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="title" class="form-label">Subject</label>
                            <input type="text" name="Subject" id="title" class="form-control" value="The Subject" />
                        </div>
                        <div class="form-group">
                            <label for="title" class="form-label">Created By:</label>
                            <input type="text" name="Created" id="author" class="form-control" value="{{ Auth::user()->name_first }} {{ Auth::user()->name_last }}" />
                        </div>
                        <div class="form-group">
                            <label for="title" class="form-label">Category:</label>
                            <select name="category" class="form-control">
                            @foreach($category as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="body" class="form-label">Answer</label>
                            <textarea name="Answer" id="Answer" rows="10" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        <button class="btn btn-success pull-right" type="submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            .create( document.querySelector( '#Answer' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
@endsection
