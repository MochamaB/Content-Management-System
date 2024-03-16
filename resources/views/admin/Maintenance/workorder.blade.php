

@if( Auth::user()->can('work-order.create') || Auth::user()->id === 1 )
<a href="{{ url('work-order/create/'.$modelrequests->id,) }}" class="btn btn-primary btn-lg text-white mb-0 me-0  float-end" role="button" style="text-transform: capitalize;">
    <i class="mdi mdi-plus-circle-outline"></i>
    Add Work order Item
</a>
@endif
 <br /><br /><br />
<div class=" contwrapper">
           

        </div>
       
        