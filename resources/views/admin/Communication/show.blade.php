@extends('layouts.admin.admin')

@section('content')
    <h1>{{ $notification->type }}</h1>
    <p>{{ $notification->data }}</p>
@endsection
