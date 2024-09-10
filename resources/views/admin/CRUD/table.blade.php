<div class=" table-responsive" id="dataTable">
    <style>
        .fixed-table-toolbar .columns-right {
            float: right !important;
            margin-left: 0px;
        }

        .fixed-table-toolbar .export .btn {
            padding: 10px 10px;

        }

        .fixed-table-toolbar .export .btn i {
            margin-right: 5px;
        }

        .fixed-table-toolbar .export .btn::before {
            content: "Export";
            /* Replace with your desired text */
            font-weight: 700;
            display: inline-block;
            margin-right: 5px;
            /* Adjust spacing as needed */
        }
    </style>


    <table style="background-color: #ffffff;" id="table"
        data-toggle="table"
        data-icon-size="sm"
        data-buttons-class="outline-primary"
        data-toolbar-align="right"
        data-buttons-align="right"
        data-search-align="left"
        data-sort-order="asc"
        data-search="true"
        data-mobile-responsive="true"
        data-sticky-header="true"
        data-pagination="true"
        data-page-list="[100, 200, 250, 500, ALL]"
        data-page-size="100"
        data-show-footer="false"
        data-side-pagination="client"
        data-show-export="true"
        data-export-types="['json','xml','csv','txt','sql','excel']"
        data-export-text=" Export Data"
        data-export-options='{
            "fileName": "exported-data",
            "ignoreColumn": ["state"]
             "exportOptions": {
                "formatExportButton": function(button) {
                    return "<button class=\"btn btn-primary btn-sm\" type=\"button\">" +
                           "<i class=\"fas fa-download\"></i> Export Data</button>";
                }
            }
        }'
        class="table">
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
                <td><a class="table" href="{{url($controller[0].'/'.$row['id'])}}">{!! $row[0] !!}</a></td>
                @foreach(array_slice($row, 2, -1) as $cell)
                <td style="text-transform: capitalize; ">{!! $cell !!}</td>
                @endforeach

                <td style="padding-right:15px;">
                    @if ($row[0] && $row['isDeleted'])
                    <a href="" class="" data-toggle="tooltip" data-placement="bottom" title="View Summary">
                        <span class="badge badge-danger">DELETED</span>
                    </a>
                    @else
                    <!-- SHOW BUTTON -->
                    <a href="{{url($controller[0].'/'.$row['id'])}}" class="" data-toggle="tooltip" data-placement="bottom" title="View Summary"><i class="mdi mdi-eye mdi-24px text-dark"></i></a>
                    <!-- EDIT BUTTON -->
                    @if( Auth::user()->can($controller[0].'.edit') || Auth::user()->id === 1)
                    <a href="{{url($controller[0].'/'.$row['id'].'/edit')}}" class=""><i class="mdi mdi-lead-pencil mdi-24px text-primary"></i></a>
                    @endif
                    <!-- DELETE BUTTON -->
                    @if( Auth::user()->can($controller[0].'.destroy') || Auth::user()->id === 1)
                    <form action="{{ url($controller[0].'/'.$row['id']) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="" style="border:0px;" data-toggle="modal" data-target="#deleteConfirmationModal{{$controller[0].$row['id'] }}"><i class="mdi mdi-delete mdi-24px text-danger"></i></button>

                        <div class="modal fade" id="deleteConfirmationModal{{$controller[0].$row['id'] }}" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color:red;">
                                        <h5 class="modal-title" id="deleteConfirmationModalLabel" style="color:white;">Confirm Deletion</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" style="font-size: 40px; color: white;">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this {{$controller[0]}}?
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
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        $(document).ready(function() {
            $('#table').bootstrapTable({
                showExport: true,
                exportOptions: {
                    formatExportButton: function(button) {
                        return '<button class="btn btn-primary btn-sm" type="button">' +
                            '<i class="fas fa-download"></i> Export Data</button>';
                    }
                }
                // ... other options
            });
        });
    </script>
</div>