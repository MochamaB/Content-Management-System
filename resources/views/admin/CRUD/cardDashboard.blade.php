<div class="row pt-4">
    <div class="col-lg-8 d-flex flex-column">
        @include('admin.CRUD.cardProgress',['cardData' => $cardDashboad])
    </div>
    <div class="col-lg-4 d-flex flex-column">
        @include('admin.Dashboard.ticketprogress',['cardData' => $cardDashboad])
    </div>
</div>