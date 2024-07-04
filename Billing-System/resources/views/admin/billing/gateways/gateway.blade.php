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
  @foreach ($gateways as $gateway)
    <form method="POST" action="{{ route('admin.billing.gateways.store', $gateway->gateway) }}">
    <h3></h3>
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="box box-secondary">
            <div class="box-header with-border">
              <h4 class="pull-left box-title">{{ $gateway->name }}</h4>
              <div class="pull-right">
                  <span class="label label-success">Recommended</span>
              </div>
            </div>
            <div class="box-body">
              <img src="/uploads/billing/{{ $gateway->gateway }}.png" style="margin-left:47%;width:150px;">
              <div class="form-group col-md-12">
                @if ($gateway->gateway == "paypal")
                <label class="control-label">Paypal Client ID</label>
                @elseif ($gateway->gateway == "stripe")
                <label class="control-label">Stripe Public Key</label>
                @elseif ($gateway->gateway == "dedipass")
                <label class="control-label">Public Key</label>
                @endif
                <div>
                  <input type="text" class="form-control" name="api" placeholder="" value="{{ $gateway->api }}">
                </div>
              </div>
              <div class="form-group col-md-12">
                @if ($gateway->gateway == "paypal")
                <label class="control-label">Paypal Client Secret Key</label>
                @elseif ($gateway->gateway == "stripe")
                <label class="control-label">Stripe Secret Key</label>
                @elseif ($gateway->gateway == "dedipass")
                <label class="control-label">Private Key</label>
                @endif
                <div>
                  <input type="text" class="form-control" name="private_key" placeholder="" value="{{ $gateway->private_key }}">
                </div>
              </div>
              <div class="form-group col-md-12">
                <label class="control-label">Mode</label>
                  <select name="mode" class="form-control">
                    <option value="sanbox" @if ($gateway->mode == "sanbox") selected @endif>SandBox</option>
                    <option value="live" @if ($gateway->mode == "live") selected @endif>Live</option>
                  </select>
              </div>
            </div>
            <div class="box-footer with-border">
              @csrf
              <button type="submit" class="btn btn-sm btn-success pull-right">Save Gateway</button>
              @if ($gateway->enabled == 1)
                <a class="btn btn-sm btn-danger" href="{{ route('admin.billing.gateways.deactivate', $gateway->gateway) }}">Desactivate</a>
              @else
                <a class="btn btn-sm btn-success" href="{{ route('admin.billing.gateways.activate', $gateway->gateway) }}">Activate</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </form>
    @endforeach
  </div>
</div>
@endsection
