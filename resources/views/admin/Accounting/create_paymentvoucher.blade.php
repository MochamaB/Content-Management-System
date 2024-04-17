@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>New Payment Voucher </h4>
    <hr>
    <form method="POST" action="{{ url('paymentvoucher') }}" class="myForm" novalidate>
        @csrf

        <div class="form-group">
            <label class="label">Property Name</label>
            <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                <option value="{{$property->id}}">{{$property->property_name}}</option>
            </select>
        </div>

        @if($model === 'units')
        <div class="form-group">
            <label class="label">Unit Number</label>
            <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                <option value="{{$unit->id}}">{{$unit->unit_number}}</option>
            </select>
        </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Name / Memo<span class="requiredlabel">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="" required list="list">
                <datalist id="list">
                    @foreach($account as $item)
                    <option value="{{$item->account_name}}">
                        @endforeach
                </datalist>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Select User Type <span class="requiredlabel">*</span></label>
                <select name="model_type" id="assigned_type" class="formcontrol2 assigned_type" placeholder="Select" required>
                    <option value="">Select Type</option>
                    <option value="App\Models\User">User</option>
                    <option value="App\Models\Vendor">Vendor</option>
                </select>
            </div>
        </div>
        <div class="col-md-6" id="vendorSelect" style="display: none;">
            <div class="form-group">
                <label class="label"> Select Vendor<span class="requiredlabel">*</span></label>
                <select id="vendorSelectElement" class="formcontrol2 " placeholder="Select">
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                    <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                    @endforeach

                </select>
            </div>
        </div>
        <div class="col-md-6" id="userSelect" style="display: none;">
            <div class="form-group">
                <label class="label"> Select User<span class="requiredlabel">*</span></label>
                <select id="userSelectElement" class="formcontrol2 " placeholder="Select">

                    <option value="">Select Value</option>
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->firstname}} {{$user->lastname}} -
                        @foreach($user->roles as $role)
                        {{ $role->name }}
                        @endforeach
                    </option>
                    @endforeach

                </select>
            </div>
        </div>

        <input type="hidden" class="form-control" id="status" name="status" value="unpaid" required readonly>
        <hr>
        <div class="col-md-10">
            <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-sticky-header="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">ACCOUNT</th>
                        <th class="text-center">DESCRIPTION</th>
                        <th class="text-center">AMOUNT</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="height:35px;">
                        <td>1.</td>
                        <td class="text-center" style="padding:0px">
                            <select name="chartofaccount_id[]" id="chartofaccount_id" class="formcontrol2" placeholder="Select" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $accounttype => $account)
                                <optgroup label="{{ $accounttype }}">
                                    @foreach($account as $item)
                                    <option value="{{ $item->id }}">{{ $item->account_name  }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center" style="padding:0px">
                            <input type="text" class="form-control " name="charge_name[]" value="" required>
                        </td>
                        <td id='' class="text-center" style="padding:0px">
                            <div style="position: relative;">
                                <span style="position: absolute; left: 10px; top: 51%; transform: translateY(-50%);">{{ $sitesettings->site_currency }}.
                                </span>
                                <input type="number" class="form-control amount money" name="amount[]" value="" placeholder="0" style="text-align: left; padding-left: 45px;" required >
                            </div>
                        </td>
                        <td class="text-center" style="background-color:#dae3fa;padding-right:20px">
                            <h5><a class=" split_rent" id="addexpense"><i class="menu-icon mdi mdi-plus-circle"> Add Item </a></i>
                            </h5>
                        </td>


                    </tr>

                </tbody>
            </table>
        </div>
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Payment Voucher</button>
        </div>

    </form>
</div>

<script>
    $(document).ready(function() {
        $('.assigned_type').on('change', function() {
            var query = this.value.trim();

            console.log(query);

            var $user = $('#userSelect');
            var $vendor = $('#vendorSelect');
            var $userSelect = $('#userSelectElement');
            var $vendorSelect = $('#vendorSelectElement');
            var $submit = $('#submit');

            // Remove the name attribute from both selects initially
            $userSelect.removeAttr('name');
            $vendorSelect.removeAttr('name');

            if (query === "App\\Models\\Vendor") {
                $user.hide();
                $vendor.show();
                $vendorSelect.show().attr('name', 'model_id'); // Set the name attribute only for the visible select
                $submit.show();
            } else if (query === "App\\Models\\User") {
                $vendor.hide();
                $user.show();
                $userSelect.show().attr('name', 'model_id'); // Set the name attribute only for the visible select
                $submit.show();
            } else {
                $userSelect.hide();
                $vendorSelect.hide();
                $submit.hide();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        
        // When the "Add Another item" link is clicked
        $('#addexpense').on('click', function(e) {
            e.preventDefault(); // Prevent the default link behavior

            // Clone the last row (including input fields)
            var newRow = $('#table tbody tr:last').clone();

            // Clear input values in the cloned row
            newRow.find('input').val('');
            // Set the initial value of the total cell to 0
        //    newRow.find('.total').text(`{{$sitesettings->site_currency}}. 0`);

            // Increment the numbering of the first td in the new row
            var currentCount = $('#table tbody tr').length;
            newRow.find('td:first').text(currentCount + 1);

            // Append the remove icon to the cloned row
            newRow.find('td:last').html('<i class="remove ti-close" style="font-size: 16px; color: red; font-weight: bold;"> Remove</i> ');

            // Append the cloned row to the table
            $('#table tbody').append(newRow);
        });

        // When the "Remove" button is clicked
        $('#table').on('click', '.remove', function() {
            // Remove the row
            $(this).closest('tr').remove();

            // Update the numbering of the remaining rows
            $('#table tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        });
    });
</script>
@endsection