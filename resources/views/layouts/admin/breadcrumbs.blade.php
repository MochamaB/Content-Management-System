<div class="" style="padding-bottom:10px;">
    <!-- Breadcrumb -->
    <nav class="d-flex"style="padding:10px;border-left:5px solid orange">
        <h6 class="mb-1">
            @php
                $routeName = Route::currentRouteName();
                $routeParts = explode('.', $routeName);
                $routeCount = count($routeParts);
            @endphp
            @foreach($routeParts as $index => $part)
                <span style="font-size:30px;text-transform:capitalize;">{{ $part }}</span>
                @if($index < $routeCount - 1)
                    <span style="font-size:30px;">/</span>
                @endif
            @endforeach
        </h6>
        <div class="btn-group ms-auto" >
                          <button type="button" onclick="history.back()" class="btn btn-outline-primary" style="border: 2px solid blue;">
                            <i class="mdi mdi-arrow-left-bold-circle mdi-24" style="font-size: 17px;color:blue"> BACK</i>
                            
                          </button>
                        
                        </div>
    </nav>
    <!-- Breadcrumb -->
    @include('layouts.admin.messages')
</div>

