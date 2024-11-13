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

        .fixed-table-toolbar .bulkaction .btn::before {
            content: "Bulk Actions";
            /* Replace with your desired text */
            font-weight: 600;
            display: inline-block;
            margin-right: 5px;
            /* Adjust spacing as needed */
        }

        .modal-body .icon-close {
            font-size: 88px;
            /* Increase the size */
            color: red;
            /* Change color to red */
            display: block;
            /* Display as block for centering */
            margin: 0 auto;
            margin-bottom: 20px;
            /* Center horizontally */
        }

        /* Center content vertically in the modal body */
        .modal-body .preview {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }
        .modal-body .message {
            word-wrap: normal;
            max-width: 90%;
            margin: 0 auto;
            font-size: 15px;
            white-space: normal;
        }
        #filter-button {
        margin-left: 0px;
        padding: 8px 12px;
        border-radius: 0px;
        border-left: blue solid 10px;
}
    </style>
    <div class="fixed-table-toolbar">
        <div class="columns columns-right btn-group float-right">
            <div class="bulkaction btn-group" style="padding-top: 10px;">
                <button id="bulk-action-btn" class="btn btn-primary btn-sm dropdown-toggle text-white"
                    aria-label="Bulk Actions" data-toggle="dropdown" type="button" style="padding:13px 10px;display:none">

                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" id="bulk-edit" data-url="{{ url($controller[0].'/bulkEdit') }}">Edit</a>
                    <a class="dropdown-item" href="#" id="bulk-delete" data-url="{{ url($controller[0].'/bulkDelete') }}">Delete</a>
                    <a class="dropdown-item" href="#" id="other-action">Other</a>

                </div>
            </div>
        </div>
    </div>



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
        data-checkbox="true"
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
                <th data-checkbox="true"></th>
                @foreach($data['headers'] as $header)
                <th data-sortable="true">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data['rows'] as $key => $row)
            <tr>
                <td></td>
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
                                    <div class="modal-header" style="background-color:red;padding: 15px 46px;">
                                        <h5 class="modal-title" id="deleteConfirmationModalLabel" style="color:white;">Confirm Deletion</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" style="font-size: 40px; color: white;">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="preview">
                                            <i class="icon-close"></i>
                                            <h3>Are you sure?</h3>
                                        </div>
                                        <p class="message">Do you really want to delete this {{ $controller[0] }}? It cant be undone.</p>
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
                    <!--- End Delete -->
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
<script>
    // Define route part outside of $(document).ready
    var currentRoutePart = "{{ $routeParts[1] ?? '' }}";
</script>
<script>
$(document).ready(function () {
    // Wait for the bootstrap table to initialize and the toolbar to load
    if (currentRoutePart === 'index') {
    setTimeout(function () {
        // Find the search input within the toolbar
        var searchInput = $('.fixed-table-toolbar .search input');

        // Only add the Filter button if it doesn't already exist
        if (searchInput.length && $('#filter-button').length === 0) {
            // Create the Filter button HTML
            var filterButton = `
                <a href="" class="btn btn-warning btn-lg text-white mb-0 me-0" id="filter-button" style="margin-left: 0px;"
                data-toggle="collapse" data-target="#collapseExampleOne" aria-expanded="false" aria-controls="collapseExampleOne">
                    <i class="mdi mdi-filter"></i> Filters
                </a>`;

            // Insert the Filter button after the search input
            searchInput.after(filterButton);
        }
    }, 500); // Adjust timeout if necessary
}

});

</script>