<div class="row">
    <div class="col-md-7">
        <div class=" contwrapper">

            @include('admin.CRUD.edit')

            <hr>
            <h5 style="text-transform: capitalize;">Amenities
            </h5>
            @if($amenities->isEmpty())
            <h6>
                <small class="text-muted">
                    No Amenities added
                </small>
            </h6>
            @else
            <div class="form-group">
                <div class="form-check">
                    @foreach($amenities as $item)
                    <label class="form-check-label label">

                        - {{ $item->amenity_name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            <button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0" data-toggle="modal" data-target="#exampleModal">
                Add Amenities
            </button>




        </div>

    </div>





    <div class="col-md-5">
        <div class=" contwrapper" style="background-color: #dfebf3;border: 1px solid #7fafd0;">

            <h5><b>Financials & Balances</b>
            </h5>
            <hr>
            <div style="display: flex; justify-content: space-between;">
                <span class="defaulttext"><b>Deposits and Pre-payments:</b></span>
                <span class="defaulttext">{{ $sitesettings->site_currency }} @currency($property->deposits->sum('totalamount'))</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span class="defaulttext"><b>Total Invoice Amount:</b></span>
                <span class="defaulttext">{{ $sitesettings->site_currency }} @currency($property->invoices->sum('totalamount'))</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span class="defaulttext"><b>Total Expenses:</b></span>
                <span class="defaulttext">{{ $sitesettings->site_currency }} @currency($property->expenses->sum('totalamount'))</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span class="defaulttext"><b>Total Payments:</b></span>
                <span class="defaulttext">{{ $sitesettings->site_currency }} @currency($property->payments->sum('totalamount'))</span>
            </div>
          


        </div>

    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:80%;">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header" style="background-color:darkblue;">
                <h5 class="modal-title" id="exampleModalLabel" style="color:white;">Add Amenities</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form id="syncForm" method="POST" action="{{ url('update-amenities/'.$actualvalues->id) }}">
                    @method('post')
                    @csrf
                    <table id="table" data-search-align="left" data-search="true" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                                <th>All Amenities</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allamenities as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input" name="amenities[]" value="{{ $item->id }}" @if($amenities->contains($item->id)) checked @endif>
                                </td>
                                <td>
                                    <label class="form-check-label">
                                        {{ $item->amenity_name }}
                                    </label>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-lg text-white mb-0 me-0" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-lg text-white mb-0 me-0">Save changes</button>
            </div>
        </div>
    </div>
</div>
@include('layouts.admin.scripts')
<script>
    // Toggle all checkboxes when the "Select All" checkbox is clicked
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="amenities[]"]');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });

    // Submit the form when the "Save" button is clicked
    document.getElementById('syncForm').addEventListener('submit', function(event) {
        event.preventDefault();
        this.submit();
    });
</script>