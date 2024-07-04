{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Addons
@endsection

@section('content-header')
    <h1>Knowledgebase<small>Here you can see information.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.knowledgebase') }}">Home</a></li>
        <li class="active">Knowledgebase</li>
    </ol>
@endsection

@section('content')

<div class="row">


    <div class="col-lg-9">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-book"></i> Knowledgebase | Categories</h3>
				@if(Auth::user()->root_admin)
                    <div class="box-tools">
                        <a style="margin-right: 0%;" href="{{ route('admin.knowledgebase.category.index') }}" class="pull-right btn btn-sm btn-primary">Go to category list</a>
                    </div>
                @endif
            </div>
            <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Information</th>
                        </tr>
                        @if(!$categorys->isEmpty())
                            @foreach($categorys as $category)
                            <tr>
                                <th>{{$category->id}}</th>
                                <th><a href="{{ route('admin.knowledgebase.category.index', $category->id) }}">{{$category->name}}</a></th>
                                <th>{{$category->description}}</th> 
                            </tr> 
                            @endforeach
                        @else
                        <tr>
                            <th></th>
                            <th></th>
                            <th><i class="fa fa-question-circle fa-spin"></i> No Category Found</th>
                        </tr>
                        @endif
                        </tbody>
                    </table>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-xs-5">
                    </div>
                    <div class="col-xs-2">
                        {{ $categorys->render() }}
                    </div>
                    <div class="col-xs-5">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header">
                Knowledgebase - Settings
            </div>
            @foreach ($settings as $setting)
            <div class="box-body">
                <div class="col-md-12">
                    <h4>Category</h4>
                    <a href="{{ route('admin.knowledgebase.category.index') }}" class="btn btn-primary"><i class="fa fa-adjust"></i> Go to Category List</a>
                </div>
                <div class="col-md-12">
                    <h4>Questions</h4>
                    <a href="{{ route('admin.knowledgebase.questions.index') }}" class="btn btn-primary"><i class="fa fa-question-circle"></i> Go to Question List</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $('tr.server-description').on('mouseenter mouseleave', function (event) {
            $(this).prev('tr').css({
                'background-color': (event.type === 'mouseenter') ? '#f5f5f5' : '',
            });
        });
    </script>
    {!! Theme::js('js/frontend/serverlist.js') !!}
@endsection
