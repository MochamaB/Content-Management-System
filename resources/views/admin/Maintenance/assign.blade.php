@extends('layouts.admin.admin')

@section('content')

<div class=" contwrapper">

    <h4>Assign Ticket</h4>
    <hr>
    <form method="POST" action="{{ url('ticket/'.$modelrequests->id) }}" class="myForm" novalidate>
        @csrf
        @method('PUT')
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">Select Assign Type <span class="requiredlabel">*</span></label>
                <select name="assigned_type" id="assigned_type" class="formcontrol2 assigned_type" placeholder="Select" required>
                    <option value="">Select Type</option>
                    <option value="App\Models\User">User</option>
                    <option value="App\Models\Vendor">Vendor</option>
                </select>
            </div>
        </div>

        <div class="col-md-6" id="vendorSelect" style="display: none;">
            <div class="form-group">
                <label class="label"> Select Vendorr<span class="requiredlabel">*</span></label>
                <select name="assigned_id" id="" class="formcontrol2 " placeholder="Select" >

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
                <select name="assigned_id" id="" class="formcontrol2 " placeholder="Select" >

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
        <div class="col-md-4" id="submit" style="display: none;">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0" style="text-transform: capitalize;" id="">Assign WorkOrder</button>
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
            var $submit = $('#submit');


            if (query === "App\\Models\\Vendor") {
               
                $user.hide();
                $vendor.show();
                $submit.show();
            } else if(query === "App\\Models\\User") {
                $user.show();
                $vendor.hide();
                $submit.show();
            } else{
                $user.hide();
                $vendor.hide();
                $submit.hide();
            }

        });


    });
</script>
@endsection