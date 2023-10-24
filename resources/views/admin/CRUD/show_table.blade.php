<div class=" table-responsive" id="dataTable">
    <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="true" data-sticky-header="true" data-pagination="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered">
        <thead>
            <tr>
                @foreach($data['headers'] as $header)
                <th data-sortable="true">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $key => $row)
            <tr>
                <td style="padding-left: 15px;"><a class="table" href="{{url($controller.'/'.$row['id'])}}">{!! $row[0] !!}</a></td>
                @foreach(array_slice($row, 2) as $cell)
                <td style="text-transform: capitalize;padding-left: 15px;">{!! $cell !!}</td>
                @endforeach
                <td>
                    <a href="{{url($controller.'/'.$row['id'])}}" class=""  data-toggle="tooltip" data-placement="bottom" title="View Summary"><i class="mdi mdi-clipboard-text mdi-24px text-dark"></i></a>
                    @if( Auth::user()->can($controller.'.edit') || Auth::user()->id === 1)
                    <a href="{{url($controller.'/'.$row['id'].'/edit')}}" class=""><i class="mdi mdi-lead-pencil mdi-24px text-primary"></i></a>
                    @endif

                    @if( Auth::user()->can($controller.'.destroy') || Auth::user()->id === 1)
                    <form action="{{ url($controller.'/'.$row['id']) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="" style="border:0px;" data-toggle="modal" data-target="#deleteConfirmationModal"><i class="mdi mdi-delete mdi-24px text-danger"></i></button>

                        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header"  style="background-color:red;">
                                        <h5 class="modal-title" id="deleteConfirmationModalLabel"  style="color:white;">Confirm Deletion</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this {{$controller}}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-danger btn-lg text-danger mb-0 me-0" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger btn-lg text-white mb-0 me-0">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>