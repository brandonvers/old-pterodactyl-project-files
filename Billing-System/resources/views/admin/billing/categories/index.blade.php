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
      <div class="box-header with-border">
        <h4 class="box-title">Categories</h4>
        <div class="box-tools search01">
            <form action="{{ route('admin.billing.categories') }}" method="GET">
                        <div class="input-group input-group-sm">
                            <input type="text" name="filter[name]" class="form-control pull-right" value="{{ request()->input('filter.name') }}" placeholder="Search Categories">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        <a href="{{ route('admin.billing.categories.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
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
                <th>Description</th>
                <th class="text-center hidden-sm hidden-xs">Priority</th>
                <th class="text-center hidden-sm hidden-xs">Visisble</th>
                <th class="text-right">Action</th>
              </tr>
              @foreach ($categories as $category)
              <tr>
                <td>{{ $category->id }}</td>
                <td><a href="{{ route('admin.billing.categories.edit', $category->id) }}">{{ $category->name }}</a></td>
                <td>{{str_limit(strip_tags($category->description), 50)}}</td>
                <td class="text-center">{{ $category->priority }}</td>
                <td class="text-center">@if ($category->visible == 1)<i class="fa fa-eye text-success text-center"></i>@else<i class="fa fa-eye-slash text-danger text-center"></i>@endif</td>
                <td class="text-right">
                  <a class="btn btn-danger btn-sm" href="{{ route('admin.billing.categories.delete', $category->id) }}">
                    <i class="fa fa-trash"></i>
                  </a>
                  <a class="btn btn-primary btn-sm" href="{{ route('admin.billing.categories.edit', $category->id) }}">
                    <i class="fa fa-edit"></i>
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
        </table>
      </div>
      <div class="box-footer no-padding">
        <div class="col-md-12 text-center">{{ $categories->links() }}</div>
      </div>
    </div>
  </div>
</div>

<script>
    var fixHelperModified = function(e, tr) {
    var $originals = tr.children();
    var $helper = tr.clone();
    $helper.children().each(function(index) {
      $(this).width($originals.eq(index).width())
    });
    return $helper;
  },
    updateIndex = function(e, ui) {
      $('td.index', ui.item.parent()).each(function (i) {
        $(this).html(i+1);
      });
      $('input[type=text]', ui.item.parent()).each(function (i) {
        $(this).val(i + 1);
      });
    };

  $("#myTable tbody").sortable({
    helper: fixHelperModified,
    stop: updateIndex
  }).disableSelection();
  
    $("tbody").sortable({
    distance: 5,
    delay: 100,
    opacity: 0.6,
    cursor: 'move',
    update: function() {}
      });
</script>
@endsection

