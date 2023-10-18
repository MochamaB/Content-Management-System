<div class=" contwrapper">
    <h4>All Utilities charged to the unit </h4>
    <hr>

    <div class=" table-responsive" id="dataTable">
        <table id="table" data-toggle="table" data-icon-size="sm" data-buttons-class="primary" data-toolbar-align="right" data-buttons-align="left" data-search-align="left" data-sort-order="asc" data-search="true" data-sticky-header="true" data-pagination="true" data-page-list="[100, 200, 250, 500, ALL]" data-page-size="100" data-show-footer="false" data-side-pagination="client" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th data-sortable="true">Charge</th>
                    <th data-sortable="true">Cycle</th>
                    <th data-sortable="true">Type</th>
                    <th data-sortable="true">Rate / Amount</th>
                    <th data-sortable="true">Recurring</th>
                    <th data-sortable="true">Last Billed</th>
                    <th data-sortable="true">Next Bill Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($charges as $item)
                <tr style="text-transform: capitalize;">
                    <td style="padding:15px;">{{$item->charge_name}}</td>
                    <td style="padding:15px;">{{$item->charge_cycle}}</td>
                    <td style="padding:15px;">{{$item->charge_type}}</td>
                    <td style="padding:15px;">{{$item->rate}}</td>
                    <td style="padding:15px;">{{$item->recurring_charge}}</td>
                    <td style="padding:15px;">{{\Carbon\Carbon::parse($item->startdate)->format('d M Y') }}</td>
                    <td style="padding:15px;">{{\Carbon\Carbon::parse($item->nextdate)->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>