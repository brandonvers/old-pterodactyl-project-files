@extends('layouts.admin')

@section('title', 'View ticket')

@section('content-header')
<h1>Viewing ticket <small>Reply to customers ticket.</small></h1>
<ol class="breadcrumb">
    <li><a href="{{ route('admin.index') }}">Admin</a></li>
    <li><a href="{{ route('admin.tickets') }}">Tickets</a></li>
    <li class="active">#{{ $ticket->ticket_id }}</li>
</ol>
@endsection

@section('title', $ticket->title)

@section('content')
<div class="row">
    <div class="col-lg-9">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">New Reply</h3>
                <a href="{{ route('admin.tickets')}}" class="box-tools btn btn-sm btn-primary">Back</a>
            </div>
            <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="form">
                <div class="box-body">
                    <input id="id" name="id" value="{{ $ticket->id }}" type="hidden" />
                    <label class="form-label"> Status</label>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="radio radio-info radio-inline">
                                <input type="radio" id="Answered" value="2" name="status" @if($ticket->status != 4 || $ticket->status != 5) checked="" @endif>
                                <label for="Answered"> <span class="label label-info">Answered</span></label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="radio radio-info radio-inline">
                                <input type="radio" id="OnHold" value="4" name="status" @if($ticket->status == 4) checked="" @endif>
                                <label for="OnHold"> <span class="label label-warning">On Hold</span></label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="radio radio-info radio-inline">
                                <input type="radio" id="InProgress" value="5" name="status" @if($ticket->status == 5) checked="" @endif>
                                <label for="InProgress"> <span class="label label-default">In Progress</span></label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="radio radio-info radio-inline ">
                                <input type="radio" id="Closed" value="0" name="status" @if($ticket->status == 0) checked="" @endif>
                                <label for="Closed"> <span class="label label-danger">Closed</span></label>
                            </div>
                        </div>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea rows="5" id="message" class="form-control" name="message"></textarea>
                    </div>
                </div>
                <div class="box-footer">
                    {!! csrf_field() !!}
                    <button type="submit" class="btn btn-primary btn-md pull-right">Submit</button>
                </div>
            </form>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $ticket->title }}</h3>
            </div>
            <div class="box-body">
                <div class="comments">
                    @foreach ($comments as $comment)
                    <div class="box box-@if($ticket->user->id === $comment->user_id){{"default"}}@else{{"success"}}@endif">
                        <div class="box-header with-border">
                            <h3 class="box-title"> 
                                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(Auth::user()->email)) }}?s=160" class="user-image" alt="User Image" style="float: left; width: 25px; height: 25px; border-radius: 50%; margin-right: 10px; margin-top: -2px;">
                                <a href="{{ route('admin.users.view', $comment->user->id) }}">{{ $comment->user->name }}</a></h3>
                            <div class="box-tools pull-right ">
                                <span class="label label-default">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="box-body">
                            {{ $comment->comment }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Ticket Info</h3>
            </div>
            <div class="box-body">
                <div class="ticket-info">
                    <p><b>Title:</b> {{ $ticket->title }}</p>
                    <p><b>Ticket ID:</b> #{{ $ticket->ticket_id }}</p>
                    <p><b>Category:</b> {{ $category->name }}</p>
                    <p><b>Priority:</b> {{ $priority->name }}</p>
                    <p>
                        @if ($ticket->status === 1)
                        <b>Status:</b> <span class="label label-success">Open</span>
                        @elseif ($ticket->status === 2)
                        <b>Status:</b> <span class="label label-info">Answered</span>
                        @elseif ($ticket->status === 3)
                        <b>Status:</b> <span class="label label-primary">Customer-Reply</span>
                        @elseif ($ticket->status === 4)
                        <b>Status:</b> <span class="label label-warning">On Hold</span>
                        @elseif ($ticket->status === 5)
                        <b>Status:</b> <span class="label label-default">In Progress</span>
                        @else
                        <b>Status:</b> <span class="label label-danger">Closed</span>
                        @endif
                    </p>
                    <p><b>Created:</b> {{ $ticket->created_at->diffForHumans() }}</p>
                    <p><b>Updated :</b> {{ $ticket->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
 @endsection
