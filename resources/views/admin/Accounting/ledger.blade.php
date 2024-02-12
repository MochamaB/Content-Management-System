@extends('layouts.admin.admin')

@section('content')
<div class=" contwrapper">
<h4 style="text-transform: capitalize;"><b> General Ledger</b></h4>
    <hr>
    @include('admin.CRUD.topfilter')

    @include('admin.CRUD.table_simple', ['data' => $tableData])



</div>
@endsection