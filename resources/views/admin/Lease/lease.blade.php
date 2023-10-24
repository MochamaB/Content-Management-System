@extends('layouts.admin.admin')

@section('content')
@if(($routeParts[1] === 'create'))

    @include('admin.CRUD.formwizard')

@elseif(($routeParts[1] === 'edit'))

<form method="POST" action="{{ url($routeParts[0].'/'.$lease->id) }}" class="myForm" enctype="multipart/form-data" novalidate>
        @method('PUT')    
        @csrf
        @include('wizard.lease.leasedetails')
    </form>
@endif

@endsection