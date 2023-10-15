<h4><b> Add Security Deposit Details</b></h4>
<hr>

<div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="mdi mdi-information text-muted me-1"></i>
        <h5><a href="{{ url('skiprent') }}" class="" id="">Click Next</a> if there is No Security Deposit</a></h5>
       
    </div>
</div><br />
<form method="POST" action="{{ url('deposit') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="col-md-6">

        <input type="hidden" class="form-control" id="property_id" name="property_id" value="{{$lease->property_id ?? ''}}">
        <input type="hidden" class="form-control" id="unit_id" name="unit_id" value="{{$lease->unit_id ?? ''}}">
        <input type="hidden" class="form-control" id="parent_utility" name="parent_utility" value="0">
        <div class="form-group">
            <h4>Security Deposit <span class="text-muted">(optional)</span></h4>
            <input type="hidden" class="form-control" id="charge_name" name="charge_name" value="security_deposit" readonly>
        </div>
    </div>
    <div class="col-md-5">
    <input type="hidden" class="form-control" name="charge_cycle" id="charge_cycle" value="once">
    </div>
    <!---------   ---->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Account<span class="requiredlabel">*</span></label>
                <select name="chartofaccounts_id" id="chartofaccounts_id" class="formcontrol2" placeholder="Select" required>
                    <option value="{{$depositcharge->chartofaccounts_id ?? ''}}">{{$depositcharge->chartofaccounts->account_name ?? 'Select Account'}}</option>
                    @foreach($accounts as $accounttype => $account)
                    <optgroup label="{{ $accounttype }}">
                        @foreach($account as $item)
                        <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <input type="hidden" class="form-control" name="charge_type" value="fixed" required readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Amount<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" name="rate" value="{{$depositcharge->rate ?? $lease->unit->security_deposit ?? ''}}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label"> Start Date<span class="requiredlabel">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate" value="{{$lease->startdate ?? ''}}" required>
                <input type="hidden" class="form-control" id="recurring_charge" name="recurring_charge" value="no">
            </div>
        </div>



    </div><br />

    @include('admin.CRUD.wizardbuttons')
</form>