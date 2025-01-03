
@extends('layouts.admin.admin')

@section('content')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    color: #ffffff !important;
    border: 0;
    border-radius: 3px;
    padding: 6px;
    font-size: .725rem;
    font-family: inherit;
    line-height: 1.2;
}
</style>
<div class=" contwrapper">
    <h4>Send Email</h4>
    <hr>
    <form method="POST" action="{{ url('email') }}" class="myForm" novalidate>
        @csrf

        <div class="col-md-7">
            <div class="form-group">
                <label class="label">Send To</label>
                <select name="send_to" id="send_to" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Value</option>
                    <option value="contact">To Contact(s)</option>
                    <option value="group">To Group</option>
                </select>
            </div>
        </div>
        <div class="col-md-10 contacts" id = "contacts" style="display:none">
            <div class="form-group">
                <label class="label"> Select Contact(s)<span class="requiredlabel">*</span></label>
                <select class="js-example-basic-multiple form-control" name="users[]" multiple="multiple">
                    @foreach($users as $item)
                    <option value="{{ $item->email }}">{{ $item->firstname }} {{ $item->lastname }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-10 groups" id = "groups" style="display:none">
            <div class="form-group">
                <label class="label"> Contact<span class="requiredlabel">*</span></label>
                <select id="users" class="formcontrolnoedit " placeholder="Select">
                    <option value="">Select Group</option>
                    @foreach($roles as $item)
                    <option value="{{ $item->name }}">{{ $item->name  }}s</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="col-md-10" id = "subject" style="display:none">        
                <div class="form-group">
                    <label class="label"> Subject<span class="requiredlabel">*</span></label>
                    <input type="text" class="form-control"  id="" name="subject" value ="">
                   
                </div>
        </div>
        <div class="col-md-10" id = "message" style="display:none">        
                <div class="form-group">
                    <label class="label"> Message<span class="requiredlabel">*</span></label>
                    <textarea class="form-control" style=" width: 100%;padding:5px;" id="" name="message">
                    </textarea>
                </div>
        </div>
        <div class="col-md-6" id="submitBtn" style="display:none">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 submitBtn" id="">Send Email</button>
        </div>


    </form>
</div>
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });
    $(document).ready(function() {
    // Listen for changes on the #send_to select input
    $('#send_to').on('change', function() {
        let selectedValue = $(this).val(); // Get the selected value (contact or group)
        
        // Hide all divs initially
        $('#contacts').hide();
        $('#groups').hide();
        $('#subject').hide();
        $('#message').hide();
        $('#submitBtn').hide();


        if (selectedValue === 'contact') {
            // Show the contact div and message div when 'contact' is selected
            $('#contacts').show();
            $('#subject').show();
            $('#message').show();
            $('#submitBtn').show();
        } else if (selectedValue === 'group') {
            // Show the group div and message div when 'group' is selected
            $('#groups').show();
            $('#subject').show();
            $('#message').show();
            $('#submitBtn').show();
        }
    });
});

</script>

@endsection