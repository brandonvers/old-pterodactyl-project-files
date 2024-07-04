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
  <div class="col-xs-12">
    <div class="nav-tabs-custom nav-tabs-floating">
      <ul class="nav nav-tabs">
          <li><a href="{{ route('admin.billing') }}">General</a></li>
          <li><a href="{{ route('admin.billing.users') }}">Users</a></li>
          <li><a href="{{ route('admin.billing.categories') }}">Categories</a></li>
          <li><a href="{{ route('admin.billing.products') }}">Products</a></li>
          <li><a href="{{ route('admin.billing.promotional-codes') }}">Promotional Codes</a></li>
          <li><a href="{{ route('admin.billing.tos') }}">TOS</a></li>
          <li class="active"><a href="{{ route('admin.billing.payoptions') }}">Gateways</a></li>
      </ul>
    </div>
  </div>
  <div class="col-xs-12">
    @if ($paygol == 1)
    <form method="POST" action="{{ route('admin.billing.paygol.edit') }}">
    @else
    <form method="POST" action="{{ route('admin.billing.paygol.store') }}">
    @endif
    <h3>@if ($paygol == 1) Edit @else Set up @endif Paygol</h3>
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="box box-secondary">
            <div class="box-header with-border">
              <h4 class="box-title">About Paygol</h4>
            </div>
            <div class="box-body">
              <p>Paygol is an online payment service provider that offers you the perfect solution to monetize your online business around the world. Paygol offers a variety of international and local payment methods including credit card, debit card, bank transfer and cash payments. Our local payment methods cover the emerging markets of Latin America and the more developed markets of Europe, Oceania, and Asia.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="box box-secondary">
            <div class="box-header with-border">
              <h4 class="pull-left box-title">Paygol</h4>
              <div class="pull-right">
                  <span class="label label-success">Recommended</span>
              </div>
            </div>
            <div class="box-body">
              <div class="form-group col-md-4">
                <label class="control-label">Paygol Service ID</label>
                <div>
                  <input type="text" class="form-control" name="service_id" placeholder="Paygol Service ID" @if ($gateway_paygol !== "none") @foreach ($gateway_paygol as $gateway) value="{{ $gateway->service_id }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Paygol Secret Key</label>
                <div>
                  <input type="text" class="form-control" name="private_key" placeholder="Secret key to validate IPNs" @if ($gateway_paygol !== "none") @foreach ($gateway_paygol as $gateway) value="{{ $gateway->private_key }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Minimum basket value</label>
                <div>
                  <input type="text" class="form-control" name="min_basket" placeholder="0.00" @if ($gateway_paygol !== "none") @foreach ($gateway_paygol as $gateway) value="{{ $gateway->min_basket }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Maximum basket value</label>
                <div>
                  <input type="text" class="form-control" name="max_basket" placeholder="0.00" @if ($gateway_paygol !== "none") @foreach ($gateway_paygol as $gateway) value="{{ $gateway->max_basket }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Percentage for using gateway</label>
                <div>
                  <input type="text" class="form-control" name="percentage_gateway" placeholder="0.00" @if ($gateway_paygol !== "none") @foreach ($gateway_paygol as $gateway) value="{{ $gateway->percentage }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Amount for using gateway</label>
                <div>
                  <input type="text" class="form-control" name="amount_gateway" placeholder="0.00" @if ($gateway_paygol !== "none") @foreach ($gateway_paygol as $gateway) value="{{ $gateway->amount }}" @endforeach @endif>
                </div>
              </div>
              <div class="form-group col-md-12">
                <p class="text-muted small">If you don't want to charge the customer a fee for using this payment gateway, fill in a 0 for the <code>Percentage</code> and/or the <code>Amount</code>.</p>
              </div>
            </div>
            <div class="box-footer with-border">
            @csrf
            @if ($paygol == 1)
            <a class="btn btn-sm btn-danger" href="{{ route('admin.billing.paygol.delete') }}">Delete Gateway</a>
            @if ($gateway_paygol !== "none")
              @foreach ($gateway_paygol as $gateway)
                @if ($gateway->enabled == 1)
                <a class="btn btn-sm btn-warning" href="{{ route('admin.billing.paygol.deactivate') }}">Set as inactive</a>
                @else
                <a class="btn btn-sm btn-success" href="{{ route('admin.billing.paygol.activate') }}">Set as active</a>
                @endif
              @endforeach
            @endif
            <button type="submit" class="btn btn-sm btn-success">Save Gateway</button>
            @else
            <button type="submit" class="btn btn-sm btn-success">Create Gateway</button>
            @endif
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
