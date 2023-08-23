@extends('layouts.admin.admin')

@section('content')
@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url($routeParts[0]) }}" id="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    @include('admin.CRUD.tabs_vertical')
</form>

@elseif(($routeParts[1] === 'edit'))
    <form method="POST" action="{{ url($routeParts[0].'/'.$user->id) }}" id="myForm" enctype="multipart/form-data" novalidate>
        @method('PUT')
        @csrf
        <div class="pageheading">
            <h4>{{$user->roles->first()->name ?? ''}}</h4>
            <p>{{$user->firstname ?? ''}} | {{$user->lastname ?? ''}} </p>
        </div>
        <br />
        @include('admin.CRUD.tabs_vertical')
    </form>
@endif

@endsection