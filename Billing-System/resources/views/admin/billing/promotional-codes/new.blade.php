@extends('layouts.admin')
@include('partials/admin.settings.nav', ['activeTab' => 'basic'])

@section('title')
    Billing
@endsection

@section('content-header')
    <h1>Billing<small>Monitor your income.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Billing</li>
    </ol>
@endsection

@section('content')
<div class="row">
      @include('admin.billing.nav')
      <div class="col-xs-12">
          <div class="box">
              <div class="box-header with-border">
                  <h3 class="box-title">Create Promotional Code</h3>
              </div>
              <form action="{{ route('admin.billing.promotional-codes.store') }}" method="POST">
                  <div class="box-body box-secondary">
                      <div class="row">
                          <div class="form-group col-md-3">
                              <label class="control-label">Code</label>
                              <div>
                                <input class="form-control" name="code" value="{{ old('code') }}">
                              </div>
                          </div>
                          <div class="form-group col-md-3">
                              <label class="control-label">Max Uses</label>
                              <div>
                                <input class="form-control" name="max_uses" value="{{ old('max_uses') }}">
                              </div>
                          </div>
                          <div class="form-group col-md-3">
                              <label class="control-label">Discount Amount</label>
                              <div>
                                <input class="form-control" name="amount" value="{{ old('amount') }}">
                              </div>
                          </div>
                          <div class="form-group col-md-3">
                              <label class="control-label">Discount Percentage</label>
                              <div class="input-group">
                                <input class="form-control" name="percentage" value="{{ old('percentage') }}">
                                <span class="input-group-addon">%</span>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="box-footer no-border no-pad-top no-pad-bottom">
                      <p class="text-muted small">If you don't want to use a amount or percentage for this promotional code, leave these options <code>0</code>. keep the max uses value <code>blank</code> if you want this code unlimited uses.</p>
                  </div>
                  <div class="box-body box-secondary">
                      <div class="row">
                          <div class="form-group col-md-4">
                              <label class="control-label">Min Basket</label>
                              <div class="input-group">
                                <input class="form-control" name="min_amount" value="{{ old('min_amount') }}">
                                <span class="input-group-addon">min</span>
                              </div>
                          </div>
                          <div class="form-group col-md-4">
                              <label class="control-label">Max Basket</label>
                              <div class="input-group">
                                <input class="form-control" name="max_amount" value="{{ old('max_amount') }}">
                                <span class="input-group-addon">MAX</span>
                              </div>
                          </div>
                          <div class="form-group col-md-4">
                              <label class="control-label">Lasts Till</label>
                              <div>
                                <input type="text" class="form-control" name="lasts_till" placeholder="{{ date('Y-m-d h:m:s') }}" @if (old('lasts_till')) value="{{old('lasts_till')}}" @else value="{{ old('lasts_till') }}" @endif>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="box-footer no-border no-pad-top no-pad-bottom">
                      <p class="text-muted small">If you don't want to use a minimum or maximum basket value for this promotional code, leave these options <code>0</code>. When it is past the lasts till timestamp, you can't use this code anymore.
                      The timestamp works as follow: <code>year-month-day hour:minute:second</code>. So if you want to let this code last till the first of januar, you do <code>2020-01-01 00:00:00</code>. set the lasts till value to <code>0</code> if you want this code to last for ever.</p>
                  </div>
                  <div class="box-footer">
                      @csrf
                      <button type="submit" class="btn btn-primary pull-right">Submit</button>
                  </div>
              </form>
            </div>
        </div>
    </div>
</div>
@endsection
