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
    
        <form method="POST" action="{{ route('admin.billing.settings.sotre') }}">
            @foreach ($billing as $settings)
            <div class="col-xs-12 col-md-12">
                <div class="box box-secondary">
                    <div class="box-header with-border">
                        <h3 class="box-title">General Settings</h3>
                    </div>
                    <div class="box-body">

                        <div class="form-group col-lg-3">
                            <label for="currency" class="control-label">Currency</label>
                            <select name="currency" class="form-control">
                                <option value="euro" @if ($settings->currency == 'euro') selected @endif>&euro; EUR</option>
                                <option value="pound" @if ($settings->currency == 'pound') selected @endif>&pound; GBP</option>
                                <option value="dollar" @if ($settings->currency == 'dollar') selected @endif>&dollar; USD</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="use_categories" class="control-label">categories</label>
                            <select name="use_categories" class="form-control">
                                <option value="0" @if ($settings->use_categories == 0) selected @endif>Disabled</option>
                                <option value="1" @if ($settings->use_categories == 1) selected @endif>Enabled</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="use_products" class="control-label">Products</label>
                            <select name="use_products" class="form-control">
                                <option value="0" @if ($settings->use_products == 0) selected @endif>Disabled</option>
                                <option value="1" @if ($settings->use_products == 1) selected @endif>Enabled</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3">
                            <label for="use_deploy" class="control-label">Deploy</label>
                            <select name="use_deploy" class="form-control">
                                <option value="0" @if ($settings->use_deploy == 0) selected @endif>Disabled</option>
                                <option value="1" @if ($settings->use_deploy == 1) selected @endif>Enabled</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            @if ($settings->use_categories == 1)
            <div class="col-xs-12 col-md-4">
                <div class="box box-secondary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Categories Settings</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group col-lg-6">
                            <label for="categories_img" class="control-label">Categories Images</label>
                            <select name="categories_img" class="form-control">
                                <option value="1" @if ($settings->categories_img == 1) selected @endif>Enable</option>
                                <option value="0" @if ($settings->categories_img == 0) selected @endif>Disable</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="categories_img_rounded" class="control-label">Images Type</label>
                            <select name="categories_img_rounded" class="form-control">
                                <option value="1" @if ($settings->categories_img_rounded == 1) selected @endif>Rounded</option>
                                <option value="0" @if ($settings->categories_img_rounded == 0) selected @endif>Normal</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="categories_img_width" class="control-label">Images Width</label>
                            <input type="text" name="categories_img_width" class="form-control" placeholder="200px" value="{{$settings->categories_img_width}}">
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="categories_img_height" class="control-label">Images Height</label>
                            <input type="text" name="categories_img_height" class="form-control" placeholder="200px" value="{{$settings->categories_img_height}}">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($settings->use_products == 1)
            <div class="col-xs-12 col-md-4">
                <div class="box box-secondary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Products Settings</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group col-lg-6">
                            <label for="products_img" class="control-label">Products Images</label>
                            <select name="products_img" class="form-control">
                                <option value="1" @if ($settings->products_img == 1) selected @endif>Enabled</option>
                                <option value="0" @if ($settings->products_img == 0) selected @endif>Disable</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="products_img_rounded" class="control-label">Images Type</label>
                            <select name="products_img_rounded" class="form-control">
                                <option value="1" @if ($settings->products_img_rounded == 1) selected @endif>Rounded</option>
                                <option value="0" @if ($settings->products_img_rounded == 0) selected @endif>Normal</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="products_img_width" class="control-label">Images Width</label>
                            <input type="text" name="products_img_width" class="form-control" placeholder="200px" value="{{$settings->products_img_width}}">
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="products_img_height" class="control-label">Images Height</label>
                            <input type="text" name="products_img_height" class="form-control" placeholder="200px" value="{{$settings->products_img_height}}">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if ($settings->use_deploy == 1)
            <div class="col-xs-12 col-md-4">
                <div class="box box-secondary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Deploy Settings</h3>
                    </div>
                    <div class="box-body">
                        Enabled !!
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-12">
                <div class="box box-secondary">
                    <div class="box-footer">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary pull-right">Save</button>
                    </div>
                </div>
            </div>
            @endforeach
        </form>
    
 </div>
@endsection