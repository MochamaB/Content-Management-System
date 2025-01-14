@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('lease') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf

    <h5><b> Complete Lease Creation</b></h5>
    <hr>
    <div class="col-md-9">
        <div class="form-group">
            <label class="label">Upload Lease Agreement<span class="requiredlabel">*</span></label>
            @include('admin.CRUD.upload_media')
            <hr>
            <label class="label">Or Generate Lease Document<span class="requiredlabel">*</span></label>


            <hr>

        </div>
    </div>
    <div class="form-group">
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="send_welcome_email" id="send_welcome_email" value="1" checked="">
        Send new Lease Agreement email to new tenant
        <i class="input-helper"></i></label>
    </div>
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input" name="send_welcome_text" id="send_welcome_text" value="1"  checked="">
        Send new Lease Agreement text message to new tenant
        <i class="input-helper"></i></label>
    </div>

  </div>

    @include('admin.CRUD.wizardbuttons')
</form>
@endif

<script>
    var pdfImage = "{{ url('uploads/pdf.png') }}";
    var txtImage = "{{ url('uploads/txt.png') }}";
    var noImage = "{{ url('uploads/nofile.png') }}";
    $(document).ready(function() {
        $('#file').change(function(e) {
            var fileName = e.target.files[0].name;
            var extension = fileName.split('.').pop().toLowerCase();

            switch (extension) {
                case 'pdf':
                    $('#fileImage').attr('src', pdfImage);
                    break;
                case 'txt':
                    $('#fileImage').attr('src', txtImage);
                    break;
                default:
                    $('#fileImage').attr('src', noImage);
            }
        });
    });
</script>