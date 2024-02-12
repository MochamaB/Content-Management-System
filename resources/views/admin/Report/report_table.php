<div class=" table-responsive" id="dataTable">
    <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="true" data-sticky-header="true" data-pagination="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered">
    <thead>
        <tr>
            @foreach($columns as $column)
                <th>{{ $column }}</th>
            @endforeach

            @foreach($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
        </tr>
    </thead>
    <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($columns as $column)
                        <td>{{ $row[$column] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>