<!---- FIRST LEVEL ----------->

@include('admin.CRUD.dashboardcards')



<!---- SECOND LEVEL ----------->
<div class="row pt-4">
    <div class="col-lg-8 d-flex flex-column">
    @include('admin.Dashboard.barchart', ['data' => $chartData])
    </div>
    <div class="col-lg-4 d-flex flex-column">
    @include('admin.Dashboard.ticketcard', ['data' => $chartData])
    </div>
</div>