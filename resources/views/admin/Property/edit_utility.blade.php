@extends('layouts.admin.admin')

@section('content')


<div class=" contwrapper">
    @if( $routeParts[1] === 'edit' && Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
    <h5>Edit Utility
        <a href="" class="editLink"> &nbsp;&nbsp;<i class="mdi mdi-lead-pencil text-primary" style="font-size:16px"></i></a>
    </h5>
    @endif

    <hr>
    <form action="{{ url('utility/'. $utility->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="label">Property Name <span class="requiredlabel">*</span></label>
                    </br>
                    <label class="label">
                        {{ $utility->property->property_name}}
                    </label>
                </div>

                <div class="form-group">
                    <label class="label">Utility Name <span class="requiredlabel">*</span></label>
                    </br>
                    <label class="label">
                        {{ $utility->utility_name}}
                    </label>
                </div>

                <div class="form-group">
                    <label class="label">Account<span class="requiredlabel">*</span></label>
                    <h6>
                        <small class="text-muted" style="text-transform: capitalize;">
                            {{ $utility->accounts->account_name}}
                        </small>
                    </h6>

                    <select name="chartofaccounts_id" id="chartofaccounts_id" class="formcontrol2" placeholder="Select" required>
                        <option value="{{$utility->chartofaccounts_id ?? ''}}">{{$utility->chartofaccounts->account_name ?? 'Select Account'}}</option>
                        @foreach($accounts as $accounttype => $account)
                        <optgroup label="{{ $accounttype }}">
                            @foreach($account as $item)
                            <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="label">Utility Type<span class="requiredlabel">*</span></label>
                    <h6>
                        <small class="text-muted" style="text-transform: capitalize;">
                            {{ $utility->utility_type }}
                        </small>
                    </h6>
                    <select name="utility_type" id="utility_type" class="formcontrol2" placeholder="Select" required>
                        <option value="{{$utility->utility_type}}"> {{ $utility->utility_type }}</option>
                        <option value="fixed"> Fixed</option>
                        <option value="units">Per Unit</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label">Billing Cycle<span class="requiredlabel">*</span></label>
                    <h6>
                        <small class="text-muted" style="text-transform: capitalize;">
                            {{ $utility->default_charge_cycle }}
                        </small>
                    </h6>
                    <select name="default_charge_cycle" id="default_charge_cycle" class="formcontrol2 charge_cycle" placeholder="Select" required>
                        <option value="{{ $utility->default_charge_cycle }}">{{ $utility->default_charge_cycle }}</option>
                        <option value="Once">Once</option>
                        <option value="Monthly"> Monthly</option>
                        <option value="Twomonths">Two Months</option>
                        <option value="Quaterly">Quaterly</option>
                        <option value="Halfyear">6 Months</option>
                        <option value="Year">1 Year</option>

                    </select>
                </div>
                <div class="form-group">
                    <label class="label">Rate or Amount<span class="requiredlabel">*</span></label>
                    </br>
                    <label class="label">
                        {{ $utility->default_rate}}
                    </label>
                    <input type="text" class="form-control" id='rate' name="default_rate" value="{{$utility->default_rate}}" required>
                </div>


            </div>
            <div class="col-md-6" style="border-left: 2px solid #dee2e6;">
                @include('admin.CRUD.information', ['message' => $information ?? ''])
                @foreach($unitCharges as $unitCharge)
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label" for="charge_{{ $unitCharge->id }}">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="selected_charges[]"
                                value="{{ $unitCharge->id }}"
                                id="charge_{{ $unitCharge->id }}" checked="">

                            {{ $unitCharge->unit->unit_number }} - {{ $unitCharge->charge_name }}
                            <i class="input-helper"></i>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>



        </div>
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Edit Utility</button>
        </div>



    </form>
</div>


@endsection