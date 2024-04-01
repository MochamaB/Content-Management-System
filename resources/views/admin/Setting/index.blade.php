@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">
    <h4></h4>
    <hr>
    @foreach($settings as $module => $name)
    <div class="d-flex align-items-center">

        <h3 class="mb-0" style="font-weight:800">{{ $module }} </h3>
    </div>

    <div class="row">
        @foreach($name as $item)
        <div class="col-md-6" style="padding:15px 15px 15px 15px;">
            <a class="table" href="{{ url('setting/'.$item->name) }}">
                <h5 style="text-transform: capitalize;">{{$item->name}}</h5>
            </a>
            <span class="text-muted" style="font-weight:500;font-style: italic">
                {{$item->description}}
            </span>
        </div>
        @endforeach
    </div>
    <hr>
    @endforeach

</div>
  @endsection