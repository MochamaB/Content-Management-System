

<div class=" table-responsive" id="dataTable">
            <table id="table"                                
                        data-toggle="table"
                        data-icon-size="sm"
                        data-buttons-class="primary"
                        data-toolbar-align="right"
                        data-buttons-align="left"
                        data-search-align="left"
                        data-sort-order="asc"
                        data-search="true"
                        data-sticky-header="true"
                        data-pagination="true"
                        data-page-list="[100, 200, 250, 500, ALL]"
                        data-page-size="100"
                        data-show-footer="false"
                        data-side-pagination="client"
             class="table table-bordered table-striped">
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
                        <td><a class="table" href="{{url($routeParts[0].'/'.$row['id'])}}">{!! $row[0] !!}</a></td>
                        @foreach(array_slice($row, 2) as $cell)
                            <td>{!! $cell !!}</td>
                        @endforeach
                        <td>
                            <a href="{{url($routeParts[0].'/'.$row['id'])}}" class="btn btn-info btn-sm text-white"><i class="mdi mdi-clipboard-text"></i>Summary</a>
                            @if( Auth::user()->can($controller[0].'.edit') || Auth::user()->id === 1)
                            <a href="{{url($routeParts[0].'/'.$row['id'].'/edit')}}" class=""><i class="mdi mdi-lead-pencil mdi-24px text-primary"></i></a>
                            @endif
                            
                            @if( Auth::user()->can($controller[0].'.create') || Auth::user()->id === 1)
                            <form action="{{ url($routeParts[0].'/'.$row['id']) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="" style="border:0px;"><i class="mdi mdi-delete mdi-24px text-danger"></i></button>
                            </form>
                            @endif
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
</div>
