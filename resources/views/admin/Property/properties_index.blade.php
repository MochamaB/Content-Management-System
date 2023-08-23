@extends('layouts.admin.admin')

@section('content')
<a href="{{ url('property/create') }}" class="btn btn-primary btn-lg text-white mb-0 me-0 float-end" role="button">
    <i class="mdi mdi-plus-circle-outline"></i>
    Add new Property
</a>
<br/><br/><br/>

<div class=" contwrapper">
    <div class="row">
   
                    @include('layouts.admin.master-filter')
                    <hr>

                    @include('admin.CRUD.table', ['data' => $tableData])

    </diV>   


</div>
<script>

</script>

@endsection
