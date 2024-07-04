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
  @foreach ($products as $product)
  <form method="POST" action="{{ route('admin.billing.products.update', $product->id) }}" enctype="multipart/form-data">
    <div class="col-xs-12">
      <div class="box box-secondary">
        <div class="box-header with-border">
          <h3 class="box-title">Edit</h3>
        </div>
        <div class="row">
            <div class="col-md-8">
              <div class="form-group col-md-4">
                <label class="control-label">Product Name</label>
                <div>
                  <input type="text" class="form-control" name="name" value="{{ $product->name }}">
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Priority</label>
                <div>
                  <input type="number" class="form-control" name="priority" value="{{ $product->priority }}">
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Price</label>
                <div>
                  <input type="text" class="form-control" name="price" value="{{ $product->price }}">
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="control-label">Category</label>
                <div>
                  <select class="form-control" name="category">
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if ($product->category == $category->id) selected @endif>({{ $category->id }}) {{ $category->name }}</option>
                  @endforeach
                  </select>
                </div>
              </div>
              <div class="form-group col-md-6">
                <label class="control-label">Visible</label>
                <select name="visible" class="form-control">
                  <option value="0" @if ($product->visible == 0) selected @endif>No</option>
                  <option value="1" @if ($product->visible == 1) selected @endif>Yes</option>
                </select>
              </div>
              <div class="form-group col-md-12">
                <label class="control-label">Description</label>
                <div>
                  <textarea class="form-control" name="description" id="description" rows="4">{!! $product->description !!}</textarea>
                </div>
              </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="form-group col-md-12">
                    <div class="image-upload">
                      <label for="imgInp">
                        <img id="img-upload" src="{{ $product->img }}" height="200" width="200" style="border-radius: 50%;">
                        <input type="file" name="select_file" id="imgInp" />
                      </label>
                    </div>
                    <p class="text-muted">Select File for Upload <strong>jpg, jpeg, png, gif</strong></p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <p class="text-muted small">You can change your currency in the general tab of the billing page. If you want to use or disable categories, you can do this in the general tab of the billing page.</p><p>
        </div>
      </div>
    </div>
    <div class="col-xs-12">
      <div class="box box-secondary">
        <div class="box-header with-border">
          <h3 class="box-title">Server Settings</h3>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="form-group col-md-4">
                <label>Egg</label>
                <select name="egg_id" class="form-control">
                  @foreach ($eggs as $egg)
                    <option value="{{ $egg->id }}" @if ($product->egg_id == $egg->id) selected @endif>({{ $egg->id }}) {{ $egg->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="node_ids" class="form-label">Nodes</label>
                <select class="form-control" id="node_ids" name="node_ids[]" multiple>
                  @foreach($nodes as $node)
                    @if (in_array($node->id, explode(',', $product->node_id)))
                      <option value="{{ $node->id }}" selected>({{ $node->id }}) {{ $node->name }}</option>
                    @else
                      <option value="{{ $node->id }}">({{ $node->id }}) {{ $node->name }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
          </div>
      </div>
    </div>
    <div class="col-xs-12">
      <div class="box box-secondary">
        <div class="box-header with-border">
          <h3 class="box-title">Resource Management</h3>
        </div>
            <div class="box-body row">
              <div class="form-group col-md-4">
                <label class="control-label">Memory</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="memory" value="{{ $product->memory }}">
                  <span class="input-group-addon">MB</span>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Swap</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="swap" value="{{ $product->swap }}">
                  <span class="input-group-addon">MB</span>
                </div>
              </div>
            </div>
            <div class="box-footer no-border no-pad-top no-pad-bottom">
              <p class="text-muted small">If you do not want to assign swap space to a server, simply put <code>0</code> for the value, or <code>-1</code> to allow unlimited swap space. If you want to disable memory limiting on a server, simply enter <code>0</code> into the memory field.</p>
            </div>
            <div class="box-body row">
              <div class="form-group col-md-4">
                <label class="control-label">Disk</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="disk" value="{{ $product->disk }}">
                  <span class="input-group-addon">MB</span>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">CPU</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="cpu" value="{{ $product->cpu }}">
                  <span class="input-group-addon">%</span>
                </div>
              </div>
              <div class="form-group col-md-4">
                <label class="control-label">Block IO Weight</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="io" value="{{ $product->io }}">
                  <span class="input-group-addon">I/O</span>
                </div>
              </div>
            </div>
            <div class="box-footer no-border no-pad-top no-pad-bottom">
              <p class="text-muted small">If you do not want to limit CPU usage, set the value to <code>0</code>. To determine a value, take the number of <em>physical</em> cores and multiply it by 100. For example, on a quad core system <code>(4 * 100 = 400)</code> there is <code>400%</code> available. To limit a server to using half of a single core, you would set the value to <code>50</code>. To allow a server to use up to two physical cores, set the value to <code>200</code>. BlockIO should be a value between <code>10</code> and <code>1000</code>. Please see <a href="https://docs.docker.com/engine/reference/run/#/block-io-bandwidth-blkio-constraint" target="_blank">this documentation</a> for more information about it.</p><p>
              </p>
            </div>
      </div>
    </div>
    <div class="col-xs-12">
      <div class="box box-secondary">
        <div class="box-header with-border">
          <h3 class="box-title">Application Feature Limits</h3>
        </div>
        <div class="box-body">
            <div class="row">
              <div class="form-group col-md-4">
                <label>Database Limit</label>
                <div>
                  <input type="text" class="form-control" name="database_limit" value="{{ $product->database_limit }}">
                </div>
                <p class="text-muted small">The total number of databases a user is allowed to create for this server.</p>
              </div>
              <div class="form-group col-md-4">
                <label>Allocation Limit</label>
                <div>
                  <input type="text" class="form-control" name="allocation_limit" value="{{ $product->allocation_limit }}">
                </div>
                <p class="text-muted small">The total number of allocation a user is allowed to create for this server.</p>
              </div>
              <div class="form-group col-md-4">
                <label>Backup Limit</label>
                <div>
                  <input type="number" class="form-control" name="backup_limit" value="{{ $product->backup_limit }}" >
                </div>
                <p class="text-muted small">The total number of backups that can be created for this server.</p>
              </div>
            </div>
        </div>
        <div class="box-footer with-border">
          @csrf
          <button type="submit" class="btn btn-sm btn-success pull-right">Update</button>
        </div>
      </div>
    </div>

  </form>
  @endforeach
</div>

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script>
$(document).ready( function() {
        $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
        });

        $('.btn-file :file').on('fileselect', function(event, label) {
            
            var input = $(this).parents('.input-group').find(':text'),
                log = label;
            
            if( input.length ) {
                input.val(log);
            } else {
                if( log ) alert(log);
            }
        
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    $('#img-upload').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInp").change(function(){
            readURL(this);
        });     
});
</script>
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
            .create( document.querySelector( '#description' ) )
            .catch( error => {
                console.error( error );
            } );

        $('#node_ids').select2({
            placeholder: 'Select Nodes',
        });
    </script>
    {!! Theme::js('vendor/lodash/lodash.js') !!}
    {!! Theme::js('js/admin/new-server.js') !!}
@endsection