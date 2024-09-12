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
                <h6 style="text-transform: capitalize;">{{$className}} Settings</h6>
            </a>
            <p class="text-muted">
            {{$item->first()->info}}
            </p>
        </div>
        @endforeach
    </div>
    <hr>

</div>
  @endsection