{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}

{{-- Billing System made by Kevko - https://mrkevko.nl --}}
@extends('layouts.admin')

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Manage your billing settings</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">Index</li>
    </ol>
@endsection

@section('content')
<div class="row">
    @include('admin.billing.nav')
  <div class="col-xs-12">
    <h3>Payment Gateways</h3>
    <div class="row mt-4">

    @foreach ($gateways as $gateway)
      <div class="col-md-4">
        <div class="box box-secondary">
          <div class="box-header with-border">
            <h4 class="pull-left box-title">{{ $gateway->name }}</h4>
            <div class="pull-right">


                  @if ($gateway->enabled == 0)
                    <span class="label label-success">Recommended</span>
                    <span class="label label-danger">Inactive</span>
                  @elseif ($gateway->enabled == 1)
                    <span class="label label-success">Recommended</span>
                    <span class="label label-primary">Active</span>
                  @endif

              
            </div>
          </div>
          <div class="box-body">
            <img src="/uploads/billing/{{ $gateway->gateway }}.png" style="margin-left:36%;width:150px;">
            <p style="margin-top: 5px;">{{ $gateway->description }}</p>
          </div>
          <div class="box-footer with-border">
            <a class="btn btn-sm btn-success pull-right" href="{{ route('admin.billing.gateways.edit', $gateway->gateway) }}">Edit Gateway</a>
          </div>
        </div>
      </div>
    @endforeach

    </div>
  </div>
</div>
@endsection
