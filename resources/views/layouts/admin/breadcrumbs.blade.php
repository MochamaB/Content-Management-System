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
    </nav>
    <!-- Breadcrumb -->
    @include('layouts.admin.messages')
</div>

