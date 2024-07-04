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
        <li><a href="{{ route('knowledgebase.index') }}">Home</a></li>
        <li class="active">Knowledgebase</li>
    </ol>
@endsection

@section('content')

<div class="row">
<div class="col-md-6">
<div class="box">
    <div class="box-header">
        Knowledgebase - Settings
    </div>
    @foreach ($settings as $setting)
    <form action="{{ route('admin.knowledgebase.settings', $setting->id) }}" method="POST">
    <div class="box-body">
        <div class="col-md-6">
            <h4 style="color: gray;">Category</h4>
            <select name="category" class="form-control">
            @if($setting->category == 1)
            <option selected value="1">Enabled</option>
            @else
            <option value="1">Enabled</option>
            @endif
            @if($setting->category == 0)
            <option selected value="0">Disabled</option>
            @else
            <option value="0">Disabled</option>
            @endif
            </select>
        </div>
        <div class="col-md-6">
            <h4 style="color: gray;">Author</h4>
            <select name="author" class="form-control">
            @if($setting->author == 1)
            <option selected value="1">Enabled</option>
            @else
            <option value="1">Enabled</option>
            @endif
            @if($setting->author == 0)
            <option selected value="0">Disabled</option>
            @else
            <option value="0">Disabled</option>
            @endif
            </select>
        </div>
        @csrf<button class="btn btn-primary pull-right" style="margin-top: 3%; margin-right: 2%;"><i class="fa fa-edit"></i> Update</button>
    </div>
    </form>
    @endforeach
</div>
</div>
<div class="col-md-6">
<div class="box">
    <div class="box-header">
        Knowledgebase - Settings
    </div>
    @foreach ($settings as $setting)
    <div class="box-body">
        <div class="col-md-6">
            <h4 style="color: gray;">Category</h4><small>Category list</small><br><br><br>
            <form method="GET" action="{{ route('admin.knowledgebase.category.list') }}">@csrf<button class="btn btn-primary"><i class="fa fa-adjust"></i> Go to Category List</button></form>
        </div>
        <div class="col-md-6">
            <h4 style="color: gray;">Questions</h4><small>Question list</small><br><br><br>
            <form method="GET" action="{{ route('admin.knowledgebase.questions.list') }}">@csrf<button class="btn btn-primary"><i class="fa fa-question-circle"></i> Go to Question List</button></form>
        </div>
    </div>
    @endforeach
</div>
</div>
</div>
<div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-book"></i> Knowledgebase | Categories</h3>
					@if(Auth::user()->root_admin)
                    <div class="box-tools">
                    <a style="margin-right: 0%;" href="{{ route('admin.knowledgebase.category.list') }}" class="pull-right btn btn-sm btn-primary">Go to category list</a>
                    </div>
                </div>
                   @endif
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
                            <th><a href="{{ route('admin.knowledgebase.category.list', $category->id) }}">{{$category->Name}}</a></th>
                            <th>{{$category->description}}</th> 
                            </tr> 
                            @endforeach
                        @else
                        <tr>
                            <th></th>
                            <th>
                            </th>
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
