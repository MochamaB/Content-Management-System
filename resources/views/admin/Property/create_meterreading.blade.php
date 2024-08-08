@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">

    <h5>New Meter reading</h5>
    <hr>
    <form method="POST" action="{{ url('meter-reading') }}" class="myForm" novalidate>
        @csrf
        @if($model === 'properties')
        <input type="hidden" class="form-control" name="model" id='' value="properties" readonly>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2 property_id" placeholder="Select" required readonly>
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <table id="table" 
            data-toggle="table" 
            data-icon-size="sm" 
            data-buttons-class="primary" 
            data-toolbar-align="right" 
            data-buttons-align="left" 
            data-search-align="left" 
            data-mobile-responsive="true"
            data-sort-order="asc"
            data-sticky-header="true" 
            data-page-list="[100, 200, 250, 500, ALL]" 
            data-page-size="100" 
            data-show-footer="false" 
            data-side-pagination="client" 
            class="table table-bordered">
                <thead>
                    <tr>

                        <th class="text-center">UNIT</th>
                        <th class="text-center">CHARGE</th>
                        <th class="text-center">DATE LAST READING </th>
                        <th class="text-center">PREVIOUS READING</th>
                        <th class="text-center">DATE OF READING </th>
                        <th class="text-center">CURRENT READING</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($charges as $key=> $item)
                    <tr style="height:35px;">
                        @php
                        $latestread = $item->meterReading->last();
                        @endphp
                        <td class="text-center" style="padding:0px">
                            <input type="hidden" class="" name="unit_id[]" id='' value="{{ $item->unit_id}}" readonly>
                            {{$item->unit->unit_number}}
                        </td>
                        <td class="text-center"style="padding:0px">
                            <input type="hidden" name="unitcharge_id[]" value="{{ $item->id }}">
                            {{$item->charge_name}}
                        </td>

                        <td data-label="DATE LAST READING" class="text-center" style="padding:0px">
                            <input type="date" class="form-control" name="startdate[]" id='startdate' value="{{ $latestread->enddate ?? old('startdate_' . $key) ?? now()->toDateString() }}" 
                            {{ $latestread ? 'readonly' : '' }}>
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="number" class="form-control @error('lastreading.' . $key) is-invalid @enderror" name="lastreading[]" id='' value="{{$latestread->currentreading ?? 0.00 }}" required {{ Auth::user()->id === 1 ||  Auth::user()->can($routeParts[0].'.edit') ? '' : 'readonly' }}>
                            @error('reading')
                            <div class="invalid-feedback">Error</div>
                            @enderror
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="date" class="form-control @error('enddate.' . $key) is-invalid @enderror" name="enddate[]" id="enddate" value="{{ old('enddate.' . $key) ??  now()->toDateString() }}" required>
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="number" class="form-control" name="currentreading[]" id="currentreading" value="{{ old('currentreading.' . $key)}}"required>
                            <input type="hidden" name="rate_at_reading[]" value="{{ $item->rate }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



        @elseif($model ==='units')

        <input type="hidden" class="form-control" name="model" id='' value="units" readonly>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Property Name</label>
                <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$property->id}}">{{$property->property_name}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Unit Number</label>
                <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                    <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Charge<span class="requiredlabel">*</span></label>
                <select name="unitcharge_id" id="unitcharge_id" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Value</option>
                    @foreach($unitcharge as $item)
                    <option value="{{$item->id}}">{{$item->charge_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">


            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Previous Reading<span class="requiredlabel">*</span></label>
                    <input type="number" class="form-control" name="lastreading" id="lastreading" value="{{$meterReading->currentreading ?? old('lastreading') ?? '0.00'}}" required {{ Auth::user()->id === 1 ||  Auth::user()->can($routeParts[0].'.edit') ? '' : 'readonly' }}>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Current Reading<span class="requiredlabel">*</span></label>
                    <input type="number" step="0.01" class="form-control" name="currentreading" value="{{old('currentreading')}}" required>
                </div>
            </div>



            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">Date of Last Reading<span class="requiredlabel">*</span></label>
                    <input type="date" class="form-control" name="startdate" id="startdate" value="{{ $meterReading->enddate ?? old('startdate') ?? now()->toDateString()}}" required 
                    {{ $meterReading ? 'readonly' : '' }}>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label class="label">End Date of Reading Period<span class="requiredlabel">*</span></label>
                    <input type="date" class="form-control" name="enddate" value="{{old('enddate') ?? now()->toDateString()}}" required>
                </div>
            </div>
        </div>
        @endif
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Record Meter Reading</button>
        </div>

    </form>
</div>





@endsection