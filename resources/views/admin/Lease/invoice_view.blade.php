
<div class="row">
    <div class="col-md-9">
        @include('admin.Lease.invoice_contents')
    </div>
    <div class="col-md-3 d-none d-lg-block">
        <div class="card">
            <div class="card-header">
                <h6>ACTIONS</h6>
            </div>
            <div class="card-body">
                @if( Auth::user()->can('payment.create') || Auth::user()->id === 1)
                @if( $invoice->status == 'unpaid' || $invoice->status == 'partially_paid')
                <a href="{{route('payment.create', ['id' => $invoice->id])}}" class="btn btn-success text-white"><i class="ti-money"></i>Record Payment</a>
                <a href="{{ url('invoice/'.$invoice->id.'/sendmail') }}" class="btn btn-primary text-white "><i class="ti-email"></i>Send Reminder</a>
                @endif
                @endif
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