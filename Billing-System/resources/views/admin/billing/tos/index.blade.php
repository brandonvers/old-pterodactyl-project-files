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
        <form method="POST" action="{{ route('admin.billing.tos.update') }}">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit the TOS</h3>
                </div>
                <div class="box-body">
                    <textarea style="width: 100%; height: 500px;" name="tos" id="body">{{ $tos }}</textarea>
                </div>
                <div class="box-footer">
                    @csrf
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script src="//cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>

    <script>
        function MinHeightPlugin(editor) {
            this.editor = editor;
        }

        MinHeightPlugin.prototype.init = function() {
            this.editor.ui.view.editable.extendTemplate({
                attributes: {
                    style: {
                        minHeight: '300px'
                    }
                }
            });
        };

        ClassicEditor.builtinPlugins.push(MinHeightPlugin);
        ClassicEditor
            .create( document.querySelector( '#body' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
    {!! Theme::js('js/admin/billing.js') !!}
@endsection
