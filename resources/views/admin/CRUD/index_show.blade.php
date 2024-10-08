
@if(($routeParts[0] === 'dashboard' || $routeParts[0] === 'payments' || $routeParts[0] === 'meter-reading' ))
@include('admin.CRUD.date_filter')
@endif



   @if (isset($cardData))
    @include('admin.CRUD.cards')
    @endif
   
   @if( Auth::user()->can($controller[0].'.create') || Auth::user()->id === 1 && ($controller[0] !== ''
   && $controller[0] !== 'media') )
    <a href="{{ url($controller[0].'/create', ['id' => $id ?? '','model' => $model ?? '']) }}" class="btn btn-primary btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;">
        <i class="mdi mdi-plus-circle-outline"></i>
        Add {{$controller[1] ?? $controller[0] }}
    </a>
    @endif
    <div class="mb-2" style="clear: both;"></div>
    <div class=" contwrapper">
        <div class="row">
            @include('admin.CRUD.table', ['data' => $tableData])


        </diV>
    </div>

