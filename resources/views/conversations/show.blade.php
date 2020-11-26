@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('conversations.users', ['users' => $users, 'unread' => $unread])
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">{{ $user->name }}</div>
                    <div class="card-body conversations">

                        @if ($messages->hasMorePages())
                            <div class="text-center">
                                <a href="{{ $messages->nextPageUrl() }}" class="btn btn-light">Previous messages</a>
                            </div>
                        @endif

                        @foreach(array_reverse($messages->items()) as $message)
                            <div class="row">
                                <div class="col-md-10 {{ $message->from->id !== $user->id ? 'offset-md-2 text-right' : '' }}">
                                    <p>
                                        <strong>{{ $message->from->id !== $user->id ? 'Moi' : $message->from->name }}</strong> <br>
                                        {!! nl2br(e($message->content)) !!}
                                    </p>
                                </div>
                            </div>
                            <hr>
                        @endforeach

                        @if ($messages->previousPageUrl())
                            <div class="text-center mb-2">
                                <a href="{{ $messages->previousPageUrl() }}" class="btn btn-light">Next messages</a>
                            </div>
                        @endif

                        <form method="post">
                            @csrf
                            <div class="form-group">
                                <textarea name="content" id="" class="form-control {{ $errors->has('content') ? 'is-invalid' : ''  }}" placeholder="Type your message..."></textarea>

                                @if ($errors->has('content'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('content') }}</strong>
                                    </span>
                                @endif

                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary">Send message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
