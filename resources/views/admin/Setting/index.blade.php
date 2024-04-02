@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">
    <h4></h4>
    <hr>
    <div class="row">
    @foreach($settings as $model_type => $item)
    @php
    $className = class_basename($model_type)
    @endphp
        <div class="col-md-6" style="padding:15px 15px 15px 15px;">
            <a class="table" href="{{ url('setting/'.$className) }}">
                <h5 style="text-transform: capitalize;">{{$className}} Settings</h5>
            </a>
            <span class="text-muted" style="font-weight:500;font-style: italic">
            {{$item->first()->info}}
            </span>
        </div>
        @endforeach
    </div>
    <hr>

</div>
  @endsection