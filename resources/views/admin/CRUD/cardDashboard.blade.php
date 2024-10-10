<div class="row">

@if(($controller[0] === 'ticket'))
    <div class="col-md-7 contwrapper mb-2">
        @include('admin.CRUD.card_title')
        @include('admin.CRUD.cardProgress',['cardData' => $cardDashboad])
    </div>
    <div class="col-md-5 contwrapper mb-2">
        @include('admin.Dashboard.ticketprogress',['cardData' => $cardDashboad])
    </div>
@endif
</div>