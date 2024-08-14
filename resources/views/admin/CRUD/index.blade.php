

<!--
@if( (Auth::user()->can($controller[0].'.create') || Auth::user()->id === 1) 
&& $controller[0] !== '' && $controller[0] !== 'media' && $controller[0] !== 'payment' )
<a href="{{ url($controller[0].'/create', ['id' => $id ?? '','model' => $model ?? '']) }}" class="btn btn-primary btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;">
    <i class="mdi mdi-plus-circle-outline"></i>
    Add {{$controller[1] ?? $controller[0] }}
</a>
@endif
<div class="mb-2" style="clear: both;"></div>
-->



<div class=" contwrapper">
    <form class="filterForm" method="GET" action="{{ url()->current() }}">
        <div class="row">

            @include('layouts.admin.master-filter')

            <hr>
            @if (isset($cardData))
<div class="accordion" id="accordionExample" style="border:none" >
    <div class="accordion-item" style="border:none ;padding:0px 0px 10px 0px" >
        <h4 class="accordion-header" id="headingOne">
            <button class="accordion-button"  style="border-left: 5px solid #0000ff;border-radius:0px" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              
              <i class="mdi mdi-arrow-down-bold-circle mdi-24" style="font-size: 17px;color:blue"> Click To View Summary</i>
            </button>
        </h4>
        <div id="collapseOne" style="border-left:5px solid #0000ff;border-bottom:1px solid #ccc;padding:15px 0px 10px 15px;" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="accordion-body row" style=" padding:0px 0px 10px 0px">
            @include('admin.CRUD.cards')
            </div>
        </div>
    </div>
</div>

@endif

            @include('admin.CRUD.table', ['data' => $tableData])


        </diV>
    </form>
</div>