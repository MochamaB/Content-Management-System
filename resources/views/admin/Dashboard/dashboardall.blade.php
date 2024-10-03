<!---- FIRST LEVEL ----------->
<div class=" contwrapper pt-0 pb-0">
    <div class="row">
        @include('admin.CRUD.cards')
    </div>
</div>



<!---- SECOND LEVEL ----------->
<div class="row pt-4">
    <div class="col-lg-8 d-flex flex-column">
        @include('admin.Dashboard.barchart', ['data' => $chartData])
    </div>
    <div class="col-lg-4 d-flex flex-column">
        @include('admin.Dashboard.paymentType')
    </div>
</div>
<!-- Third Level -------->
<div class="row pt-4">
    <div class="col-lg-4 d-flex flex-column">
        @include('admin.Dashboard.totaltax')
    </div>
    <div class="col-lg-8 d-flex flex-column">
        @include('admin.Dashboard.ticketcard', ['data' => $chartData])
    </div>
</div>