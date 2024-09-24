<div class="row">
    <div class="col-md-9">

        @include('admin.Lease.payment_contents')
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>ACTIONS</h5>
            </div>
            <div class="card-body">
                <a href="" onclick="printDiv('printMe')" class="btn btn-warning text-white"><i class="icon-printer" style="color:white"></i> Print to PDF</a>
            </div>
        </div>

    </div>

</div>
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;

    }
</script>