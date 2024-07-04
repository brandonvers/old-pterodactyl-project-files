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
        <li><a href="{{ route('admin.knowledgebase.index') }}">Home</a></li>
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
                    <a style="margin-right: 0%;" href="{{ route('admin.knowledgebase.questions.new') }}" class="pull-right btn btn-sm btn-primary">Create New</a>
                    </div>
                </div>
                   @endif
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Subject</th>
                            @foreach ($settings as $setting)
                            @if($setting->author == 1)
                            <th>Author</th>
                            @endif
                            @endforeach
                            <th>Last Update</th>
                            <th class="pull-right">Actions</th>
                        </tr>
                        @if(!$questions->isEmpty())
                            @foreach($questions as $question)
                            <tr>
                            <th>{{$question->id}}</th>
                            <th><a href="{{ route('knowledgebase.page', $question->id) }}">{{$question->subject}}</a></th>
                            @foreach ($settings as $setting)
                            @if($setting->author == 1)
                            <th>{{$question->author}}</th>
                            @endif
                            @endforeach
                            <th style="color: gray;">{{$question->updated_at}}</th>
                            <td>
						        <form method="POST" action="{{ route('admin.knowledgebase.questions.delete', $question->id) }}">@csrf<button class="btn btn-danger pull-right"><i class="fa fa-trash-o"></i></button></form>
                                <form style="margin-right: 15%;" action="{{ route('admin.knowledgebase.questions.edit', $question->id) }}">@csrf<button class="btn btn-primary pull-right"><i class="fa fa-edit"></i></button></form>
                            </td> 
                            </tr> 
                            @endforeach
                        @endif
                        @if($questions->isEmpty())
                        <tr>
                            <th></th>
                            <th></th>
                            @foreach ($settings as $setting)
                            @if($setting->author == 1)
                            <th></th>
                            @endif
                            @endforeach
                            <th>
                            <i class="fa fa-question-circle fa-spin"></i>
                            No Questions Found
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
                    {{ $questions->render() }}
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
