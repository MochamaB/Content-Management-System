@extends('layouts.admin.admin')

@section('content')
@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url($routeParts[0]) }}" id="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    @include('admin.CRUD.wizard')
</form>

@elseif(($routeParts[1] === 'edit'))
    <form method="POST" action="{{ url($routeParts[0].'/'.$user->id) }}" id="myForm" enctype="multipart/form-data" novalidate>
        @method('PUT')
        @csrf
      
        @include('admin.CRUD.tabs_vertical')
    </form>
@endif

@endsection