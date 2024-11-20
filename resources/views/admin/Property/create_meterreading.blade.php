@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">

    <h5>New Meter reading</h5>
    <hr>
    <form method="POST" action="{{ url('meter-reading') }}" class="myForm" novalidate>
        @csrf
    
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
                @if($model === 'properties')
                    @foreach($charges as $key=> $item)
                    <tr style="height:35px;">
                        @php
                        $latestread = $item->meterReading->last();
                        $item->startdate = \Carbon\Carbon::parse($item->startdate)->toDateString();
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
                            <input type="date" class="form-control" name="startdate[]" id='startdate' value="{{ $latestread->enddate ?? old('startdate_' . $key) ??  $item->startdate }}" 
                            {{ $latestread ? 'readonly' : '' }}>
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="number" class="form-control  @if(session('statuserror')) is-invalid @endif" name="lastreading[]" id='' value="{{$latestread->currentreading ?? 0.00 }}" required {{ Auth::user()->id === 1 ||  Auth::user()->can($routeParts[0].'.edit') ? '' : 'readonly' }}>
                            @error('reading')
                            <div class="invalid-feedback">Error</div>
                            @enderror
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="date" class="form-control  @if(session('statuserror')) is-invalid @endif" name="enddate[]" id="enddate" value="{{ old('enddate.' . $key) ??  now()->toDateString() }}" required>
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="number" class="form-control  @if(session('statuserror')) is-invalid @endif" name="currentreading[]" id="currentreading" value="{{ old('currentreading.' . $key)}}"required>
                            <input type="hidden" name="rate_at_reading[]" value="{{ $item->rate }}">
                        </td>
                    </tr>
                    @endforeach
                @elseif($model ==='units')
                    @foreach($charges as $key=> $item)
                    <tr style="height:35px;">
                        @php
                        $latestread = $item->meterReading->last();
                        $item->startdate = \Carbon\Carbon::parse($item->startdate)->toDateString();
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
                            <input type="date" class="form-control" name="startdate[]" id='startdate' value="{{ $latestread->enddate ?? old('startdate_' . $key) ?? $item->startdate }}" 
                            {{ $latestread ? 'readonly' : '' }}>
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="number" class="form-control  @if(session('statuserror')) is-invalid @endif" name="lastreading[]" id='' value="{{$latestread->currentreading ?? 0.00 }}" required {{ Auth::user()->id === 1 ||  Auth::user()->can($routeParts[0].'.edit') ? '' : 'readonly' }}>
                            @error('reading')
                            <div class="invalid-feedback">Error</div>
                            @enderror
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="date" class="form-control  @if(session('statuserror')) is-invalid @endif" name="enddate[]" id="enddate" value="{{ old('enddate.' . $key) ??  now()->toDateString() }}" required>
                        </td>
                        <td  class="text-center" style="padding:0px">
                            <input type="number" class="form-control  @if(session('statuserror')) is-invalid @endif" name="currentreading[]" id="currentreading" value="{{ old('currentreading.' . $key)}}"required>
                            <input type="hidden" name="rate_at_reading[]" value="{{ $item->rate }}">
                        </td>
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>



      
      
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Record Meter Reading</button>
        </div>

    </form>
</div>





@endsection