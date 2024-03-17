@if( Auth::user()->can('work-order.create') || Auth::user()->id === 1 )
<a href="{{ url('workorder-expense/create/'.$tickets->id) }}" class="btn btn-outline-primary btn-lg mb-0 me-3 float-end" role="button" style="text-transform: capitalize;">
    <i class="mdi mdi-plus-circle-outline"></i>
    Add Expense
</a>
<a href="{{ url('work-order/create/'.$tickets->id,) }}" class="btn btn-primary btn-lg text-white mb-0 me-3 float-end" role="button" style="text-transform: capitalize;">
    <i class="mdi mdi-plus-circle-outline"></i>
    Add Work order Item
</a>



@endif
<br /><br /><br />
<div class=" contwrapper">



    <ul class="bullet-line-list">
        <li>
            @foreach($workorders as $item)
            <div class="d-flex justify-content-between">
                <div><span class="text-light-green">{{$item->users->firstname}} {{$item->users->lastname}} - {{$item->users->roles->first()->name}}</span>
                    <p class="text-muted mb-2 fw-bold">{{\Carbon\Carbon::parse($item->created_at)->format('d M Y')}}</p>
                    <p class="text-muted">
                        {{$item->notes}}
                    </p>
                </div>
                
            </div>
            @endforeach
        </li>
      
     
      
    </ul>
    <div class="list align-items-center pt-3">
        <div class="wrapper w-100">
            <p class="mb-0">
               
            </p>
        </div>
    </div>


    <!-- Section: Timeline -->

    <!-- Section: Timeline -->
</div>