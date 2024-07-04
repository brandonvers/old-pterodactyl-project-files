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
                <h3 class="box-title">Invoices History</h3>
                <div class="box-tools">
                    <a href="{{ route('admin.billing.invoices.new') }}" class="btn btn-sm btn-primary">Create New</a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>User</th>
                        <th></th>
                    </tr>
                    @foreach($invoices as $invoice)
                        @if ($invoice->reason == 'Top up Credit')
                            <tr>
                                <td><b>#{{ $invoice->id }}</b></td>
                                <td>@foreach ($billing as $settings) &{{ $settings->currency }}; @endforeach {{ number_format($invoice->amount, 2) }}</td>
                                <td>{{ date(__('d-m-Y'), strtotime($invoice->created_at)) }}</td>
                                <td>{{ $invoice->user->getNameAttribute() }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.billing.invoices.pdf', ['id' => $invoice->id]) }}"><i class="fa fa-file-pdf-o"></i></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
            <div class="box-footer no-padding">
              <div class="col-md-12 text-center">{{ $invoices->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection