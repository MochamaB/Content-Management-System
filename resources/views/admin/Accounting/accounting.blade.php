@extends('layouts.admin.admin')

@section('content')

    
    @include('admin.CRUD.topfilter')
   
    @if(($routeParts[1] === 'ledger'))
    @include('admin.Accounting.general_ledger')

    @else
    @include('admin.Accounting.income_statement')

    @endif



@endsection