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
    <div class="box box-secondary">
      <div class="box-header">
        <h3 class="box-title">Products</h3>
        <div class="box-tools search01">
            <form action="{{ route('admin.billing.products') }}" method="GET">
                <div class="input-group input-group-sm">
                    <input type="text" name="filter[name]" class="form-control pull-right" value="{{ request()->input('filter.name') }}" placeholder="Search Products">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        <a href="{{ route('admin.billing.products.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                    </div>
                </div>
            </form>
        </div>
      </div>
      <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tbody>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th class="text-center hidden-sm hidden-xs">Memory</th>
                <th class="text-center hidden-sm hidden-xs">CPU</th>
                <th class="text-center hidden-sm hidden-xs">Disk</th>
                <th class="text-center hidden-sm hidden-xs">Priority</th>
                <th class="text-center hidden-sm hidden-xs">Visisble</th>
                <th class="text-right">Action</th>
              </tr>
              @foreach ($billing as $setting)
                  @foreach ($products as $product)
                    <tr>
                      <td>{{ $product->id }}</td>
                      <td><a href="{{ route('admin.billing.products.edit', $product->id) }}">{{ $product->name }}</a></td>
                      <td>&{{ $setting->currency }};{{ $product->price }}</td>
                      <td>{{str_limit(strip_tags($product->description), 50)}}</td>
                      <td class="text-center">{{ $product->memory }} MB</td>
                      <td class="text-center">{{ $product->cpu }} %</td>
                      <td class="text-center">{{ $product->disk }} MB</td>
                      <td class="text-center">{{ $product->priority }}</td>
                      <td class="text-center">@if ($product->visible == 1)<i class="fa fa-eye text-success text-center"></i>@else<i class="fa fa-eye-slash text-danger text-center"></i>@endif</td>
                      <td class="text-right">
                        <a class="btn btn-danger btn-sm" href="{{ route('admin.billing.products.delete', $product->id) }}">
                          <i class="fa fa-trash"></i>
                        </a>
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.billing.products.edit', $product->id) }}">
                          <i class="fa fa-edit"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
              @endforeach
          </tbody>
        </table>
      </div>
                <div class="box-footer no-padding">
                    <div class="col-md-12 text-center">{{ $products->links() }}</div>
                </div>
    </div>
  </div>
</div>
@endsection
