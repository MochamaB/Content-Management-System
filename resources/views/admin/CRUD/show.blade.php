@extends('layouts.admin.admin')

@section('content')

<div class="pageheading">
    <h4>{{$pageheadings[0]}}</h4>
    <p>{{$pageheadings[1]}} <span class="mb-2" style="font-size:20px"> | </span>{{$pageheadings[2]}}</p>
</div>

@include('admin.CRUD.tabs_horizontal')

@endsection