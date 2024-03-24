@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">
    <h4 style="text-transform: capitalize;"><b> Charges Due for Invoice Generation</b></h4>
    <hr>
    @if( Auth::user()->can($controller[0].'.create') || Auth::user()->id === 1)
    <form method="POST" action="{{ url('generateinvoice') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 float-end" id="submitBtn">
            <i class="mdi mdi-plus-circle-outline"></i>
            Generate All Invoices Due</button>
    </form>
    @endif
    @if( Auth::user()->can('meter-reading.create') || Auth::user()->id === 1 )
    <a href="{{ url('meter-reading/create/') }}" class="btn btn-outline-primary btn-lg mb-0 me-3 float-end" role="button" style="text-transform: capitalize; font-weight:700">
        <i class="mdi mdi-plus-circle-outline"></i>
        Add Meter Readings
    </a>
    @endif
    <br /><br />
    <hr>
    @include('admin.CRUD.table', ['data' => $tableData])


</div>





@endsection