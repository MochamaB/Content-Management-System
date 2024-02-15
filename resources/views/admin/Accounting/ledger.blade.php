@extends('layouts.admin.admin')

@section('content')
<div class=" contwrapper">
    <h4 style="text-transform: capitalize;"><b> General Ledger</b></h4>
    <hr>
    @include('admin.CRUD.topfilter')

    @if(($routeParts[1] === 'general_ledger'))
    @include('admin.CRUD.table_simple', ['data' => $tableData])

    @else
    @include('admin.Accounting.income_statement')

    @endif


</div>
@endsection