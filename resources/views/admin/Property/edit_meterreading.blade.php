@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">
@if( $routeParts[1] === 'edit' && Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <h5>Edit Meter reading
        <a href="" class="editLink">Edit</a>
        </h5>
        @endif

<hr>
<form action="{{ url('meter-reading/'. $meterReading->id) }}" method="POST">
        @csrf
        @method('PUT')
        
       


            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Previous Reading<span class="requiredlabel">*</span></label>
                    <h6>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $meterReading->lastreading}}
                    </small>
                    </h6>
                    <input type="number" class="form-control" name="lastreading" id="lastreading" value="{{$meterReading->lastreading ?? old('lastreading') ?? '0.00'}}" required readonly>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Current Reading<span class="requiredlabel">*</span></label>
                    <h6>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $meterReading->currentreading }}
                    </small>
                    </h6>
                    <input type="number" step="0.01" class="form-control" name="currentreading" value="{{$meterReading->currentreading ?? old('currentreading')}}" required>
                </div>
            </div>



           
        
   
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit Meter Reading</button>
        </div>

    </form>
</div>





@endsection