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
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-3">
                <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="fa fa-globe"></i></span>
                    <div class="info-box-content number-info-box-content">
                        <span class="info-box-text">All Time Income </span>
                        <span class="info-box-number">@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach{{ number_format($alltime_income, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="fa fa-globe"></i></span>
                    <div class="info-box-content number-info-box-content">
                        <span class="info-box-text">{{ date('Y')}} Income </span>
                        <span class="info-box-number">@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach{{ number_format($this_year_income, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="ion ion-ios-calendar"></i></span>
                    <div class="info-box-content number-info-box-content">
                        <span class="info-box-text">{{ date('F') }} Income</span>
                        <span class="info-box-number">@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach{{ number_format($this_month_income, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="ion ion-ios-pricetags"></i></span>
                    <div class="info-box-content number-info-box-content">
                        <span class="info-box-text">User Balance left</span>
                        <span class="info-box-number">@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach{{ number_format($user_accounts, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        By Country
                    </div>
                    <div class="box-body">
                        <canvas id="country_chart" width="100%" height="50"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        By Month
                    </div>
                    <div class="box-body">
                        <canvas id="month_chart" width="100%" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        var income_month_graph = JSON.parse('{!! json_encode($income_month_graph) !!}');
        var income_country_graph = JSON.parse('{!! json_encode($income_country_graph) !!}');
    </script>    
    {!! Theme::js('vendor/chartjs/chart.min.js') !!}
    {!! Theme::js('js/admin/billing.js') !!}
@endsection
