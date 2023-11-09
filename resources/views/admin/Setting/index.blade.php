@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

  @foreach ($settings as $module => $moduleData)
      <div class="d-flex align-items-center">
      <i class="mdi mdi mdi-{{$moduleData['icon']}}" style="color: #007bff; font-size: 2rem; padding-right:14px;"></i>
        <h4 class="mb-0">{{$module}} Settings</h4>
      </div>
          <hr><br />
  <div class="row">
    @foreach($moduleData['submodules'] as $submodule)
    <div class="col-md-6" style="padding-bottom:30px;">
      <a class=""  href="{{ url('setting/'.$submodule) }}">
        <h5 style="text-transform: capitalize;">{{$submodule}}</h5>
      </a>
      <div class="media">
        <div class="media-body">
          <p class="card-text"></p>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endforeach
</div>

  @endsection