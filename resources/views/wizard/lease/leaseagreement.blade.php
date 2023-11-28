@if(($routeParts[1] === 'create'))
<form method="POST" action="{{ url('lease') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="col-md-8">
        <div class="form-group">
            <label class="label">Upload Lease Agreement<span class="requiredlabel">*</span></label>
            <input type="file" name="leaseagreement" class="form-control" id="file" required />
            <img id="fileImage" src="{{ url('resources/uploads/nofile.png') }}" style="height: 200px; width: 200px;">
        </div>
    </div>

    @include('admin.CRUD.wizardbuttons')
</form>
@endif

<script>
    var pdfImage = "{{ url('resources/uploads/pdf.png') }}";
    var txtImage = "{{ url('resources/uploads/txt.png') }}";
    var noImage = "{{ url('resources/uploads/nofile.png') }}";
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