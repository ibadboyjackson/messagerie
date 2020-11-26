@extends('layouts.app')

@section('content')

    <div id="messagerie" data-base="{{ route('conversations', [], false) }}">
        <Messagerie :user="{{ \Illuminate\Support\Facades\Auth::user()->id }}"></Messagerie>
    </div>

@stop
