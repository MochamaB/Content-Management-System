<style>
    .center-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        /* Centers the text above the image */
        min-height: 300px;
        /* Optional: Ensures a consistent height */
    }

    .responsive-image {
        max-width: 100%;
        /* Ensures the image does not overflow */
        height: auto;
        /* Maintains the aspect ratio */
        max-height: 400px;
        /* Optional: Caps the image height */
        object-fit: contain;
        /* Ensures the image fits within its container */
        margin-top: 15px;
        /* Adds space between text and image */
    }
</style>
<h4><b> Outstanding Balances</b></h4>
<hr>

@if($financialCheck['outstandingInvoices']->isNotEmpty())
<h6><strong>Total Outstanding Amount:</strong>
    <span style="color:red"> {{ $sitesettings->site_currency }} {{ $financialCheck['totalOutstanding'] }}</span>

</h6>
</br>
@include('admin.CRUD.table', ['data' => $invoiceTableData,
'controller' =>'lease'])
@else
<br />
<div class="center-content">
    <h6 style="color:blue"> <i>No Outstanding Balance</i></h6>
    <img class="responsive-image" src="{{ url('uploads/vectors/nobalance.png') }}" alt="No Balance">
</div>
@endif
<form method="POST" action="{{ url('lease/' . $lease->id . '/financecheck') }}" class="myForm" enctype="multipart/form-data" novalidate>
    @csrf
    <input type="hidden" name="outstanding" value="{{ $financialCheck['totalOutstanding'] }}">
    
    @include('admin.CRUD.wizardbuttons')
</form>