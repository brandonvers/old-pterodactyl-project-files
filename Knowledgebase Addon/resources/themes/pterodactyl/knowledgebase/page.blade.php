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
	<div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $question->subject }}</h3>
                    @foreach($settings as $setting)
                        @if($setting->category == 1)
                        <div class="box-tools">
                            <a href="{{ route('knowledgebase.list', $question->category) }}" class="btn btn-sm btn-primary">Go back</a>
                        </div>
                        @else
                        <div class="box-tools">
                            <a href="{{ route('knowledgebase.index') }}" class="btn btn-sm btn-primary">Go back</a>
                        </div>
                        @endif
                    @endforeach
                </div>
                <div class="box-body">
                {!! $question->information !!}
                </div>
                <div class="box-footer">
                    <h6 style="color: gray;">© {{$question->author}} - {{ $time }}</h6>
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
