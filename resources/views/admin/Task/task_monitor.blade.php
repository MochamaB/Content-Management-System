<div class=" contwrapper">
<h4>List of Details of all command runs</h4>
<hr>

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
                    <tbody >
                        @foreach($data['rows'] as $key => $row)
                            <tr >
                                @foreach($row as $cell)
                                    <td  style="padding:15px;">{!! $cell !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
</div>