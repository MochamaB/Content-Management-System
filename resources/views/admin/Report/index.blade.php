@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">
    <h4></h4>
    <hr>
    @foreach($reports as $module => $submodules)
    <div class="d-flex align-items-center">

        <h3 class="mb-0" style="font-weight:800">{{ $module }} Reports</h3>
    </div>

    <div class="row">
        @foreach($submodules as $report)
        <div class="col-md-6" style="padding:15px 15px 15px 15px;">
            <a class="table" href="{{ url('report/'.$report->id) }}">
                <h5 style="text-transform: capitalize;">{{$report->title}}</h5>
            </a>
            <span class="text-muted" style="font-weight:500;font-style: italic">
                {{$report->description}}
            </span>
        </div>
        @endforeach
    </div>
    <hr>
    @endforeach

</div>
@endsection