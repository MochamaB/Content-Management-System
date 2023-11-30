@extends('layouts.admin.admin')

@section('content')
@if(($routeParts[1] === 'create'))

    @include('admin.CRUD.formwizard')

@else

<div class=" contwrapper">

<form method="POST" action="{{ url('generateinvoice') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit:Lease Details</button>
        </div>
</form>

</div>

 @endif      
  


@endsection