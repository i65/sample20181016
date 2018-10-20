@extends('layouts.default')
@section('title', '所有用户')
@section('content')
<div class="col-md-offset-2 col-md-8">
    <h2>所有用户</h2>
    <ul class="users">
        @foreach($users as $user)
        @include('users._user')
        @endforeach 
    </ul>

    {!! $users->render() !!}
</div>
@stop 