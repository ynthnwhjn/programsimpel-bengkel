@extends('ps::layouts.admin')

@section('content')
<h1>Welcome, {{ $session_user->name }}</h1>
@endsection

