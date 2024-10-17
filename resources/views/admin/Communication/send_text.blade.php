<div class="" style="padding:20px">
    <h4>Send Text Message</h4>
    <hr>
    <form method="POST" action="{{ url('textmessage') }}" class="myForm" novalidate>
        @csrf

        <div class="col-md-7">
            <div class="form-group">
                <label class="label">Send To</label>
                <select name="send_to" id="send_to" class="formcontrol2" placeholder="Select" required>
                    <option value="">Select Value</option>
                    <option value="contact">To Contact</option>
                    <option value="group">To Group</option>
                </select>
            </div>
        </div>
        <div class="col-md-10 contacts" id = "contacts" style="display:none">
            <div class="form-group">
                <label class="label"> Select Contact(s)<span class="requiredlabel">*</span></label>
                <select class="js-example-basic-multiple formcontrolnoedit" name="users[]" multiple="multiple">
                    @foreach($users as $item)
                    <option value="{{ $item->phonenumber }}">{{ $item->firstname }} {{ $item->lastname }}</option>
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
        <div class="col-md-10" id = "message" style="display:none">        
                <div class="form-group">
                    <label class="label"> Message<span class="requiredlabel">*</span></label>
                    <textarea class="form-control" style=" width: 100%;padding:5px;" id="" name="message">
                    </textarea>
                </div>
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
        $('#message').hide();

        if (selectedValue === 'contact') {
            // Show the contact div and message div when 'contact' is selected
            $('#contacts').show();
            $('#message').show();
        } else if (selectedValue === 'group') {
            // Show the group div and message div when 'group' is selected
            $('#groups').show();
            $('#message').show();
        }
    });
});

</script>