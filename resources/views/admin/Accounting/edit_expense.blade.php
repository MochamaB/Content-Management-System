@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    
        @if( $routeParts[1] === 'edit' && $instance->status !=='paid' && Auth::user()->can($routeParts[0].'.edit') || Auth::user()->id === 1)
        <h4>Edit Expense
        <a href="" class="editLink">Edit</a>
        </h4>
        @endif
    
    <hr>
    <form method="POST" action="{{ url('expense/'.$instance->id) }}" class="myForm" novalidate>
        @csrf
        <div class="row">
            <!--- --------- ROW 1 ----->
            <div class="col-md-6" style="border-right: 2px solid #dee2e6;padding-right:30px">

                <div class="form-group">
                    <label class="label">Property Name</label>
                    <h6>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $property->property_name }}
                    </small>
                    </h6>
                    <select name="property_id" id="property_id" class="formcontrol2" placeholder="Select" required readonly>
                        <option value="{{$property->id}}">{{$property->property_name}}</option>
                    </select>
                </div>

                @if($instance->unit_id)
                <div class="form-group">
                    <label class="label">Unit Number</label>
                    <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $instance->unit->unit_number }}
                    </small>
                    </h5>
                    <select name="unit_id" id="unit_id" class="formcontrol2" placeholder="Select" required readonly>
                        <option value="{{$instance->unit->id}}">{{$instance->unit->unit_number}}</option>
                    </select>
                </div>
                @endif

                <div class="form-group">
                    <label class="label">Name of the Expense<span class="requiredlabel">*</span></label>
                    <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $instance->name }}
                    </small>
                    </h5>
                    <input type="text" class="form-control" id="charge_name" name="name" value="{{ $instance->name }}" required>
                </div>

                <input type="hidden" class="form-control" name="model_type" value="App\Models\Vendor" required>
                
                @if($instance->model_type === 'App\Models\Ticket')
                <div class="form-group">
                    <label class="label"> Charged To<span class="requiredlabel">*</span></label>
                    <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        PROPERTY/COMPANY
                    </small>
                    </h5>
                </div>
                @else
                <div class="form-group">
                    <label class="label"> Pay To Vendor<span class="requiredlabel">*</span></label>
                    <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $instance->model->name}}
                    </small>
                    </h5>
                    <select name="model_id" class="formcontrol2 " placeholder="Select" required>
                        <option value="">{{ $instance->model->name}}</option>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                        @endforeach

                    </select>
                </div>
               @endif

                <div class="form-group">
                    <label class="label"> Due on Date<span class="requiredlabel">*</span></label>
                    <h5>
                    <small class="text-muted" style="text-transform: capitalize;">
                        {{ $instance->duedate}}
                    </small>
                    </h5>
                    <input type="date" class="form-control" id="duedate" name="duedate" value="{{ $instance->duedate}}" required>
                </div>

            </div>
            <!------- - ROW 2 -->
            <div class="col-md-6 d-none d-md-block">

                <div class="form-group">
                    <label class="label">Upload Receipt / Attachment</label>
                    <input type="file" name="receipt" value="" class="form-control" id="" />
                    <img class="img-fluid" id="logo-image-before-upload" src="{{ url('uploads/vectors/addfile.png') }}">

                </div>

            </div>

        </div>
        <hr>
        @include('admin.Accounting.edit_table')
        <hr>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="submitBtn">Add Expense</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        // Add change event listener to charge_cycle select element
        $("#charge_cycle").change(function() {
            // Get the selected value
            var selectedValue = $(this).val();
            // alert(selectedValue);

            // Update the recurring_charge select element based on the selected value
            if (selectedValue === "Once") {
                $("#recurring_charge").val("no");
                // Show and set the charge_type to "Fixed" and make it readonly
                $("#charge_type").val("fixed").attr("readonly", "readonly");
            } else {
                $("#recurring_charge").val("yes");
                // Reset and enable the charge_type input
                $("#charge_type").val("Select Value").removeAttr("readonly");
            }
        });
    });
</script>
@endsection