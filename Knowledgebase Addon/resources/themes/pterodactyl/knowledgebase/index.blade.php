{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.master')

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
@foreach ($settings as $setting)
    @if(!$setting->category == 1)
	<div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-book"></i> Knowledgebase</h3>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Subject</th>
                            <th>Author</th>
                            <th>Last Update</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        @if(!$questions->isEmpty())
                            @foreach($questions as $question)
                            <tr>
                            <th>{{$question->id}}</th>
                            <th><a href="{{ route('knowledgebase.page', $question->id) }}">{{$question->subject}}</a></th>
                            <th>{{$question->author}}</th>
                            <th style="color: gray;">{{$question->updated_at}}</th>
                            </tr> 
                            @endforeach
                        @endif
                        @if($questions->isEmpty())
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><i class="fa fa-question-circle fa-spin"></i>
                            No Questions Found
                            </th>
                            <th></th>
                            <th></th>
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
        @else 
                        @if(!$categorys->isEmpty())
                            @foreach($categorys as $category)
            <div class="col-md-3">
                <div class="box">
                    <div class="box-header">
                    	<h3 class="box-title">{{$category->Name}}</h3>
                    </div>
                    <div class="box-body">
                    	<p>{{$category->description}}</p>
                    </div>
                    <div class="box-footer">
                        @csrf
						<form action="{{ route('knowledgebase.list', $category->id) }}">
                    	<button "style="display:inline-block;" class="pull-right btn btn-sm btn-success"><i class="icon"></i>Go to {{$category->Name}}</button>
						</form>
                    </div>
                </div>
              </div>
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
                    {{ $categorys->render() }}
                </div>
                <div class="col-xs-5">
                </div>
            </div>
            </div>
            </div>
        @endif
@endforeach
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
