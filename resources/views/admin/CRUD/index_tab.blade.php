@extends('layouts.admin.admin')

@section('content')

<form class="filterForm" method="GET" action="{{ url()->current() }}">
    @if (isset($cardData))
    <div class=" contwrapper mb-2">
        <div class="row">
            @include('admin.CRUD.card_title')
          
            @include('admin.CRUD.cards')

           
        </div>
    </div>
    @endif
    <div class=" contwrapper">
        <div class="row">
            @include('layouts.admin.master-filter')

            <hr>

            @include('admin.CRUD.table', ['data' => $tableData])
        </div>

    </div>

</form>

@include('admin.CRUD.tabs_horizontal')

@endsection