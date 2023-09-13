
<form method="POST" action="{{ url('savelease') }}" id="myForm" enctype="multipart/form-data" novalidate>
@csrf

<div class="col-md-5">
    <div class="row">
        <div class="col-md-6">
            <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0 previousBtn">Previous: Utilities</button>
        </div>
        <div class="col-md-6">
            <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0 " id="">Create New Lease</button>
        </div>
    </div>
</div>
</form>