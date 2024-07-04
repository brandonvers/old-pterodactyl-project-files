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
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-book"></i> Knowledgebase</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.knowledgebase') }}" class="btn btn-sm btn-primary">Go back</a>
                        <a style="margin-left: 10px;" href="{{ route('admin.knowledgebase.questions.new') }}" class="pull-right btn btn-sm btn-primary">Create New</a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Subject</th>
                            <th>Category</th>
                            @foreach ($settings as $setting)
                            @if($setting->author == 1)
                            <th>Author</th>
                            @endif
                            @endforeach
                            <th>Last Update</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        @if(!$questions->isEmpty())
                            @foreach($questions as $question)
                            <tr>
                            <th><code>{{$question->id}}</code></th>
                            <th><a href="">{{$question->subject}}</a></th>
                            @foreach($categories as $category)
                            @if($category->id == $question->category)
                            <th>{{$category->name}}</th>
                            @endif
                            @endforeach
                            @foreach ($settings as $setting)
                            @if($setting->author == 1)
                            <th>{{$question->author}}</th>
                            @endif
                            @endforeach
                            <th>{{$question->updated_at}}</th>
                            <td class="text-right">
						        <a class="btn btn-xs btn-danger" data-action="delete" data-id="{{ $question->id }}"><i class="fa fa-trash"></i></a>
                                <a href="{{ route('admin.knowledgebase.questions.edit', $question->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></a>
                            </td> 
                            </tr> 
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="box-footer no-padding">
                    <div class="col-md-12 text-center">{{ $questions->render() }}</div>
                </div>
            </div>
        </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $('[data-action="delete"]').click(function (event) {
            event.preventDefault();
            let self = $(this);
            swal({
                title: '',
                type: 'warning',
                text: 'Are you sure you want to delete this question?',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#d9534f',
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                cancelButtonText: 'Cancel',
            }, function () {
                $.ajax({
                    method: 'DELETE',
                    url: '/admin/knowledgebase/questions/delete',
                    headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
                    data: {
                        id: self.data('id')
                    }
                }).done((data) => {
                    swal({
                        type: 'success',
                        title: 'Success!',
                        text: 'You have successfully deleted this question.'
                    });

                    self.parent().parent().slideUp();
                }).fail((jqXHR) => {
                    swal({
                        type: 'error',
                        title: 'Ooops!',
                        text: (typeof jqXHR.responseJSON.error !== 'undefined') ? jqXHR.responseJSON.error : 'A system error has occurred! Please try again later...'
                    });
                });
            });
        });
    </script>
@endsection
