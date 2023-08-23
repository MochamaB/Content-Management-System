<div class="" style="padding-bottom:10px;">
    <!-- Breadcrumb -->
    <nav class="d-flex">
        <h6 class="mb-0">
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
    </nav><br/>
    <!-- Breadcrumb -->
    @include('layouts.admin.messages')
</div>

