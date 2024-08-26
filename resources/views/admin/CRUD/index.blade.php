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
        
          
            @if (isset($cardData))
            @include('admin.CRUD.cards')
            

            @endif
            @include('layouts.admin.master-filter')

            <hr>

            @include('admin.CRUD.table', ['data' => $tableData])


        </diV>
    </form>
</div>