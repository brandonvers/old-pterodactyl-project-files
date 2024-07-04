{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Deploy.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">Deploy</li>
    </ol>
@endsection

@section('content')
    <div class="row">
    @include('admin.billing.nav')

    @foreach ($nests as $nest)
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$nest->name}} Settings</h3>
                </div>

                <form method="POST" action="{{ route('admin.billing.deploy.update', $nest->id) }}">
                    <div class="box-body">

                        <div class="form-group col-lg-3">
                            <label for="database_limit" class="control-label">Database Limit</label>
                            <input type="text" name="database_limit" class="form-control" placeholder="" value="{{$nest->database_limit}}">
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="allocation_limit" class="control-label">Allocation Limit</label>
                            <input type="text" name="allocation_limit" class="form-control" placeholder="" value="{{$nest->allocation_limit}}">
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="memory_monthly_cost" class="control-label">Memory Monthly Cost</label>
                            <input type="text" name="memory_monthly_cost" class="form-control" placeholder="" value="{{$nest->memory_monthly_cost}}">
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="disk_monthly_cost" class="control-label">Disk Monthly Cost</label>
                            <input type="text" name="disk_monthly_cost" class="form-control" placeholder="" value="{{$nest->disk_monthly_cost}}">
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="cpu_limit" class="control-label">CPU Limit</label>
                            <input type="text" name="cpu_limit" class="form-control" placeholder="" value="{{$nest->cpu_limit}}">
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="max_memory" class="control-label">Max Memory</label>
                            <input type="text" name="max_memory" class="form-control" placeholder="" value="{{$nest->max_memory}}">
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="max_disk" class="control-label">Max Disk</label>
                            <input type="text" name="max_disk" class="form-control" placeholder="" value="{{$nest->max_disk}}">
                        </div>

                    </div>
                    <div class="box-footer">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary pull-right">Save</button>
                    </div>
                </form>

            </div>
        </div>
    @endforeach

    </div>
@endsection

@section('footer-scripts')
    @parent
@endsection
