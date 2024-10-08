@if($routeParts[0] !=='dashboard')
<div class="" style="padding:10px 0px 0px 10px;border-left:5px solid #ffaf00;margin-bottom:10px;">
    <!-- Breadcrumb -->
    <nav class="d-flex">
        <h3 class="mb-0">
            @php
            $routeName = Route::currentRouteName();
            $routeParts = explode('.', $routeName);
            $routeCount = count($routeParts);
            @endphp

            @foreach($routeParts as $index => $part)
            <span style="text-transform:capitalize;">
                @if($part === 'index')
                    Manage
                @else
                    {{ $part }}
                @endif
            </span>
            @if($index < $routeCount - 1) <span style="font-size:20px;">/</span>
                @endif
                @endforeach
        </h3>
        @if ($routeCount > 1 && $routeParts[1] != 'index')
        <button type="button" onclick="history.back()" class="btn btn-outline-primary ms-auto mb-0 me-0 d-none d-lg-block">
            <i class="mdi mdi-arrow-left-bold-circle mdi-24"> BACK</i>
        </button>
        @elseif(isset($controller)  && (Auth::user()->can($controller[0].'.create') || Auth::user()->id === 1) 
            && $controller[0] !== '' && $controller[0] !== 'media' && $controller[0] !== 'payment' )
            <a href="{{ url($controller[0].'/create', ['id' => $id ?? '','model' => $model ?? '']) }}" class="btn btn-primary btn-lg text-white ms-auto me-0" role="button" style="text-transform: capitalize;">
                <i class="mdi mdi-plus-circle-outline"></i>
                Add {{$controller[1] ?? $controller[0] }}
            </a>
        @endif


    </nav>
    @if(!empty($pageheadings))
    <h5 style="text-transform: capitalize;">{{$pageheadings[0] ?? ""}}</h5>
    <p style="text-transform: capitalize;">{{$pageheadings[1] ?? ""}} <span class="mb-2"> | </span>{{$pageheadings[2] ?? ""}}</p>
    @endif


</div>
<!-- Breadcrumb -->
@include('layouts.admin.messages')
@endif