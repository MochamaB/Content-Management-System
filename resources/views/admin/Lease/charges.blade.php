@extends('layouts.admin.admin')

@section('content')
@if(($routeParts[1] === 'create'))

    @include('admin.CRUD.formwizard')

@elseif(($routeParts[1] === 'edit'))

<div class=" contwrapper">

        @if($unitcharge->charge_name === 'rent')
            @include('wizard.lease.rent')
        @else
        @include('wizard.lease.utilities')
        @endif

</div>

       
  
@endif

@endsection