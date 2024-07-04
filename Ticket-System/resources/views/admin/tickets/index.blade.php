@extends('layouts.admin')

@section('title', 'List Tickets')

@section('content-header')
<h1>Tickets <small>All created tickets in the system.</small></h1>
<ol class="breadcrumb">
    <li><a href="{{ route('admin.index') }}">Admin</a></li>
    <li><a href="{{ route('admin.tickets') }}" class="active">Tickets</a></li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Ticket List</h3>
                    <div class="box-tools search01">
                        <form action="{{ route('admin.tickets') }}" method="GET">
                            <div class="input-group input-group-sm">
                                <input type="text" name="query" class="form-control pull-right" value="{{ request()->input('query') }}" placeholder="@lang('strings.search')">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
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
                            <th>Status</th>
                            <th>Ticket ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>User</th>
                            <th class="text-center">Last Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        @foreach ($tickets as $ticket)
                        <tr>
                            <td>
                                <code>#{{ $ticket->id }}</code>
                            </td>
                            <td>
                                @if ($ticket->status === 1)
                                <span class="label label-success">Open</span>
                                @elseif ($ticket->status === 2)
                                <span class="label label-info">Answered</span>
                                @elseif ($ticket->status === 3)
                                <span class="label label-primary">Customer-Reply</span>
                                @elseif ($ticket->status === 4)
                                <span class="label label-warning">On Hold</span>
                                @elseif ($ticket->status === 5)
                                <span class="label label-default">In Progress</span>
                                @else
                                <span class="label label-danger">Closed</span>
                                @endif
                            </td>
                            <td>
                                <code>#{{ $ticket->ticket_id }}</code>
                            </td>
                            <td>
                                {{ $ticket->title }}
                            </td>
                            <td>
                                @foreach ($categories as $category)
                                @if ($category->id === $ticket->category_id)
                                {{ $category->name }}
                                @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach ($priorities as $priority)
                                @if ($priority->id === $ticket->priority_id)
                                {{ $priority->name }}
                                @endif
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('admin.users.view', $ticket->user->id) }}">
                                    {{ $ticket->user->name }} ({{ $ticket->user->username }}) {{ $ticket->user->servers_count }}
                                </a>
                            </td>
                            <td class="text-center">{{ $ticket->updated_at->diffForHumans() }}</td>
                            <td class="text-right">
                                <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST">
                                    @if ($ticket->status !== 0)
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-xs btn-danger">Close</button>
                                    @endif
                                    <a href="{{ route('admin.tickets.view', $ticket->id) }}" class="btn btn-xs btn-primary">View</a>
                                    <a class="btn btn-xs btn-danger" data-action="delete" data-id="{{ $ticket->id }}"><i class="fa fa-trash"></i> Delete</a>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="box-footer no-padding">
                    <div class="col-md-12 text-center">{{ $tickets->render() }}</div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $('[data-action="delete"]').click(function (event) {
            event.preventDefault();
            let self = $(this);
            swal({
                title: '',
                type: 'warning',
                text: 'Are you sure you want to delete this ticket?',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#d9534f',
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                cancelButtonText: 'Cancel',
            }, function () {
                $.ajax({
                    method: 'DELETE',
                    url: '/admin/tickets/delete',
                    headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
                    data: {
                        id: self.data('id')
                    }
                }).done((data) => {
                    swal({
                        type: 'success',
                        title: 'Success!',
                        text: 'You have successfully deleted this ticket.'
                    });

                    self.parent().parent().slideUp();
                }).fail((jqXHR) => {
                    swal({
                        type: 'error',
                        title: 'Ooops!',
                        text: (typeof jqXHR.responseJSON.error !== 'undefined') ? jqXHR.responseJSON.error : 'A system error has occurred! Please try again later...'
                    });
                });
            });
        });
    </script>
@endsection