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
<div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-book"></i> Knowledgebase</h3>
					@if(Auth::user()->root_admin)
                    <div class="box-tools">
                    <a href="{{ route('admin.knowledgebase.index') }}" class="btn btn-sm btn-primary">Go back</a>
                    <a style="margin-right: 0%;" href="{{ route('admin.knowledgebase.category.new') }}" class="pull-right btn btn-sm btn-primary">Create New</a>
                    </div>
                </div>
                   @endif
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Information</th>
                        </tr>
                        @if(!$categories->isEmpty())
                            @foreach($categories as $category)
                            <tr>
                            <th><a href="{{ route('admin.knowledgebase.category.list', $category->id) }}">{{$category->Name}}</a></th>
                            <th>{{$category->description}}</th>
                            <td>
						        <form method="POST" action="{{ route('admin.knowledgebase.category.delete', $category->id) }}">@csrf<button class="btn btn-danger pull-right"><i class="fa fa-trash-o"></i></button></form>
                                <form style="margin-right: 15%;" action="{{ route('admin.knowledgebase.category.edit', $category->id) }}">@csrf<button class="btn btn-primary pull-right"><i class="fa fa-edit"></i></button></form>
                            </td> 
                            </tr> 
                            @endforeach
                        @else
                        <tr>
                            <th></th>
                            <th></th>
                            <th>
                            <i class="fa fa-question-circle fa-spin"></i>
                            No Category Found
                            </th>
                            <th></th>
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
                    {{ $categories->render() }}
                </div>
                <div class="col-xs-5">
                </div>
            </div>
            </div>
            </div>
            </DIV>

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
