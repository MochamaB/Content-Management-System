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
    <br /><br /><br />
    @include('admin.CRUD.table', ['data' => $tableData])


</div>





@endsection