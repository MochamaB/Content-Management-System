@extends('layouts.admin.admin')

@section('content')
@if(($routeParts[1] === 'create'))

    @include('admin.CRUD.wizard')

@elseif(($routeParts[1] === 'edit'))

    @include('admin.CRUD.tabs_vertical')
@endif

@endsection