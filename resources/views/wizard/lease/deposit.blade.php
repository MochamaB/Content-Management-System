@if(($routeParts[1] === 'create'))
<h5><b> Add Security Deposit Details</b></h5>
<hr>



<div class="" id="depositinfo">
<div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="btn-danger float-end" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p><a href="{{ url('skipdeposit') }}" class=" btn btn-outline-danger text-danger mb-0 nextBtn" id="nextBtn">
                <i class="mdi mdi mdi-arrow-right-bold-circle me-1"></i>
                <b>Skip To Next</b></a> if there is No Security Deposit</p>
    </div>

    <form method="POST" action="{{ url('securitydeposit') }}" class="myForm" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="col-md-6">

            <input type="hidden" class="form-control" id="property_id" name="property_id" value="{{$lease->property_id ?? ''}}">
            <input type="hidden" class="form-control" id="unit_id" name="unit_id" value="{{$lease->unit_id ?? ''}}">
            <div class="form-group">
                <input type="hidden" class="form-control" id="charge_name" name="charge_name" value="security deposit" readonly>
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
                        <option value="{{$defaultDepositAccount->id ?? $depositcharge->chartofaccounts_id ?? ''}}">
                            {{$defaultDepositAccount->account_name ?? $depositcharge->chartofaccounts->account_name ?? 'Select Account/ Security Liability'}}
                        </option>
                        @foreach($depositaccounts as $accounttype => $account)
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
                    <div class="input-group">
                        <span class="input-group-text spanmoney">{{$sitesettings->site_currency}}</span>
                        <input type="number" class="form-control money" name="rate" value="{{$depositcharge->rate ?? $lease->unit->security_deposit ?? ''}}" required>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="label"> Payment Date<span class="requiredlabel">*</span></label>
                    <input type="date" class="form-control" id="startdate" name="startdate" value="{{$lease->startdate ?? ''}}" required>
                    <input type="hidden" class="form-control" id="recurring_charge" name="recurring_charge" value="no">
                </div>
            </div>



        </div><br />

        @include('admin.CRUD.wizardbuttons')
    </form>
</div>
@elseif(($routeParts[1] === 'edit'))
<h4 style="text-transform: capitalize;">Edit Security Deposit &nbsp;
    @if( Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
    <a href="" class="editLink">Edit</a>
</h4>
@endif
<hr>



@endif
<script>
    $(document).ready(function() {
        // Check if $depositcharge is not null
        // Check the initial value of $depositcharge
        let depositcharge = '{{ $depositcharge ?? 0 }}';

        //    alert(depositcharge);

        if (depositcharge != 0) {
            $('#depositinfo').show();
            $('#skipdeposit').hide();
            $('#depositselect').hide();
        }
        $('#depositstatus').change(function() {
            var selectedValue = $(this).val();

            if (selectedValue === 'Yes') {
                $('#depositinfo').show();
                $('#depositselect').hide();
                $('#skipdeposit').hide();
            } else {
                $('#depositinfo').hide();
                $('#skipdeposit').show();
            }
        });
    });
</script>