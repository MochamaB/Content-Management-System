@extends('layouts.admin.admin')

@section('content')
<div class=" contwrapper">
    
    @include('admin.CRUD.topfilter')
   
    @if(($routeParts[1] === 'ledger'))
    @include('admin.CRUD.table_simple', ['data' => $tableData])

    @else
    @include('admin.Accounting.income_statement')

    @endif


</div>
@endsection