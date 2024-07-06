<div class="row">
    <div class="col-md-9">
        @include('admin.lease.invoice_contents')
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h4>ACTIONS</h4>
            </div>
            <div class="card-body">
                @if( Auth::user()->can('payment.create') || Auth::user()->id === 1)
                <a href="{{route('payment.create', ['id' => $invoice->id])}}" class="btn btn-success btn-lg text-white"><i class="ti-money"></i>Record Payment</a>
                <a href="{{ url('invoice/'.$invoice->id.'/sendmail') }}" class="btn btn-primary btn-lg text-white "><i class="ti-email"></i>Send Reminder Email</a>
                @endif
                <a href="" onclick="printDiv('printMe')" class="btn btn-warning btn-lg text-white"><i class="icon-printer" style="color:white"></i> Print to PDF</a>
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