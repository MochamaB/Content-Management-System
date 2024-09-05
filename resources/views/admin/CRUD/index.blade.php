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


<form class="" method="GET" action="{{ url()->current() }}">
    @if (isset($cardData))
    <div class=" contwrapper mb-2">
        <div class="row">
            @include('admin.CRUD.card_title')
          
            @include('admin.CRUD.cards')

           
        </div>
    </div>
    @endif
    <div class=" contwrapper">
        <div class="row">
            @include('layouts.admin.master-filter')

            <hr>

            @include('admin.CRUD.table', ['data' => $tableData])
        </div>

    </div>

</form>