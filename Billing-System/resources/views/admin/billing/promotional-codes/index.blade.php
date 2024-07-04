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
                <h3 class="box-title">Promotional Codes</h3>
                <div class="box-tools search01">
                    <form action="{{ route('admin.billing.promotional-codes') }}" method="GET">
                        <div class="input-group input-group-sm">
                            <input type="text" name="filter[code]" class="form-control pull-right" value="{{ request()->input('filter.code') }}" placeholder="Search Codes">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <a href="{{ route('admin.billing.promotional-codes.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
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
                      <th>Code</th>
                      <th>Percentage</th>
                      <th>Amount</th>
                      <th class="text-center hidden-sm hidden-xs">Uses</th>
                      <th class="text-center hidden-sm hidden-xs">Max Uses</th>
                      <th class="text-center hidden-sm hidden-xs">Lasts till</th>
                      <th class="text-right">Action</th>
                    </tr>
                    @foreach ($promotional_codes as $code)
                      <tr>
                        <td>{{ $code->id }}</td>
                        <td><code>{{ $code->code }}</code></a></td>
                        <td>{{ $code->percentage }}%</td>
                        <td>${{ $code->amount }}</td>
                        <td class="text-center">{{ $code->uses }} x</td>
                        <td class="text-center">
                          @if (!$code->max_uses) 
                            Unlimited
                          @else
                            {{ $code->max_uses }}
                          @endif
                        </td>
                        <td class="text-center">@if ($code->lasts_till !== '0000-00-00 00:00:00') {{ $code->lasts_till }} @else Forever @endif</td>
                        <td class="text-right">
                          <a class="btn btn-danger btn-sm" href="{{ route('admin.billing.promotional-codes.delete', $code->id) }}">
                            <i class="fa fa-trash"></i>
                          </a>
                          <a class="btn btn-primary btn-sm" href="{{ route('admin.billing.promotional-codes.edit', $code->id) }}">
                            <i class="fa fa-edit"></i>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
            <div class="box-footer no-padding">
              <div class="col-md-12 text-center">{{ $promotional_codes->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
