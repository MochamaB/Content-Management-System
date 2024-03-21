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

@if($workorders->isEmpty())

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <h4 style="color:blue"> <i>There are no work order items yet.Add Work Order Item</i></h4>
        <img class=" float-center" style="width:450px; height:300px" src="{{ url('uploads/vectors/addtasks.png') }}">
        </div>
        <div class="col-md-3"></div>
    </div>
@else

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
                <div class="d-flex">
                    @if( Auth::user()->can('work-order.edit') || Auth::user()->id === 1)
                    <a href="{{url('work-order/'.$item->id.'/edit')}}" class=""><i class="mdi mdi-lead-pencil mdi-24px text-primary"></i></a>
                    @endif
                  
                   <!-- DELETE BUTTON -->
                   @if( Auth::user()->can('work-order.destroy') || Auth::user()->id === 1)
                    <form action="{{ url('work-order/'.$item->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="" style="border:0px;" data-toggle="modal" data-target="#deleteConfirmationModal{{$item->id}}"><i class="mdi mdi-delete mdi-24px text-danger"></i></button>

                        <div class="modal fade" id="deleteConfirmationModal{{$item->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header"  style="background-color:red;">
                                        <h5 class="modal-title" id="deleteConfirmationModalLabel"  style="color:white;">Confirm Deletion</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" style="font-size: 40px; color: white;">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this work-order item ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-danger btn-lg text-danger mb-0 me-2" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger btn-lg text-white mb-0 me-0">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </li>



    </ul>

    @endif

    <!-- Section: Timeline -->

    <!-- Section: Timeline -->
</div>