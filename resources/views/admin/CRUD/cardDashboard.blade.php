<style>

.equal-height {
    max-height: 250px;
    min-height: 250px;
    overflow: auto;
    display: flex;
    flex-direction: column;
}
</style>

<div class="row mb-2 gx-3" style="display: flex; align-items: stretch;">
    <div class="col-md-7">
        <div class="p-4 border bg-white equal-height">
        @if(($controller[0] === 'ticket'))
            @include('admin.CRUD.card_title')
            @include('admin.CRUD.cardProgress',['cardData' => $cardDashboad])
        @elseif($controller[0] === 'invoice')
            @include('admin.CRUD.card_title')
            @include('admin.Dashboard.widgets.totalcard',['cardData' => $cardDashboad])
        @endif
        </div>
    </div>
    <div class="col-md-5">
        <div class="p-4 border bg-white equal-height">
        @if(($controller[0] === 'ticket'))
            @include('admin.CRUD.card_title')
            @include('admin.CRUD.cardProgress',['cardData' => $cardDashboad])
        @elseif($controller[0] === 'invoice')
           
        @include('admin.Dashboard.charts.doughnutChart', ['chartData' => $chartData])
        @endif
          
        </div>
    </div>
</div>

