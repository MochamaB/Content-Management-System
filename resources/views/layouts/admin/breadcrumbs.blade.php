@if($routeParts[0] !=='dashboard')
<div class="" style="padding:10px 0px 0px 10px;border-left:5px solid #ffaf00;margin-bottom: 15px;">
    <!-- Breadcrumb -->
    <nav class="d-flex"style="">
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
                          <button type="button" onclick="history.back()" class="btn btn-outline-primary ms-auto" 
                                  style="border: 2px solid blue;margin-right:0px;">
                            <i class="mdi mdi-arrow-left-bold-circle mdi-24" style="font-size: 17px;color:blue"> BACK</i>
                            
                          </button>
                          
                        
    </nav>
    @if(!empty($pageheadings))
    <h4 style="text-transform: capitalize;">{{$pageheadings[0] ?? ""}}</h4>
    <p style="text-transform: capitalize;">{{$pageheadings[1] ?? ""}} <span class="mb-2" style="font-size:20px"> | </span>{{$pageheadings[2] ?? ""}}</p>
   @endif
    
   
</div>
 <!-- Breadcrumb -->
 @include('layouts.admin.messages')
@endif


