{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Users.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">Users</li>
    </ol>
@endsection

@section('content')
    <div class="row">
    @include('admin.billing.nav')
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">User List</h3>
                    <div class="box-tools search01">
                        <form action="{{ route('admin.billing.users') }}" method="GET">
                       <div class="input-group input-group-sm">
                            <input type="text" name="filter[email]" class="form-control pull-right" value="{{ request()->input('filter.email') }}" placeholder="Search">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Client Name</th>
                                <th>Username</th>
                                <th>balance</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td><code>{{ $user->id }}</code></td>
                            <td><a href="{{ route('admin.users.view', $user->id) }}">{{ $user->email }}</a> @if($user->root_admin)<i class="fa fa-star text-yellow"></i>@endif</td>
                            <td>{{ $user->name_last }}, {{ $user->name_first }}</td>
                            <td>{{ $user->username }}</td>
                            <form method="POST" action="{{ route('admin.billing.users.update', $user->id) }}">
                                <th><input name="balance" type="number" value="{{ $user->balance }}" class="form-control"></input></th>
                                <th>@csrf<button class="btn btn-success">Save</button></th>
                            </form>
                            <td class="text-center"><img src="https://www.gravatar.com/avatar/{{ md5(strtolower($user->email)) }}?s=100" style="height:20px;" class="img-circle" /></td>
                        </tr> 
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="box-footer no-padding">
                    <div class="col-md-12 text-center">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
@endsection
