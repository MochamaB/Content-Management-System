@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">
    <h4></h4>
    @foreach($reports as $module => $submodules)
    <div class="d-flex align-items-center">

        <h5 class="mb-0" style="font-weight:600">{{ $module }} Reports</h5>
    </div>
    <hr>
    <div class="row">
        @foreach($submodules as $report)
        <div class="col-md-6" style="padding:15px 15px 15px 15px;">
            <a class="table" href="{{ url('report/'.$report->id) }}">
                <h6 style="text-transform: capitalize;">{{$report->title}}</h6>
            </a>
            <p class="text-muted">
                {{$report->description}}
            </p>
        </div>
        @endforeach
    </div>
    
    @endforeach

</div>
@endsection