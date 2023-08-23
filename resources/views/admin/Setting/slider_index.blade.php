@extends('layouts.admin.admin')

@section('content')

<a href="{{ url('slider/create') }}" class="btn btn-primary btn-lg text-white mb-0 me-0 float-end" role="button">
    <i class="mdi mdi-plus-circle-outline"></i>
    Add new Slider
</a>
<br/><br/><br/>

<div class=" contwrapper">
    <diV class="row">
   
                   
                    @include('layouts.admin.master-filter')
                    <hr>

                    @include('admin.CRUD.table', ['data' => $tableData])

    </diV>   


</div>

@endsection